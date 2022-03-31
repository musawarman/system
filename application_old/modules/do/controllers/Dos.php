<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * module       jemiro kasih
 */

/**
 * Class Dos
 */
class Dos extends Admin_Controller
{
    /**
     * dos constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_dos');
    }

    public function index()
    {
        // Display all dos by default
        redirect('do/status/all');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of dos to load
        switch ($status) {
            case 'draft':
                $this->mdl_dos->is_draft();
                break;
            case 'sent':
                $this->mdl_dos->is_sent();
                break;
            case 'viewed':
                $this->mdl_dos->is_viewed();
                break;
            case 'approved':
                $this->mdl_dos->is_approved();
                break;
            case 'rejected':
                $this->mdl_dos->is_rejected();
                break;
            case 'canceled':
                $this->mdl_dos->is_canceled();
                break;
        }

        $this->mdl_dos->paginate(site_url('dos/status/' . $status), $page);
        $dos = $this->mdl_dos->result();

        $this->layout->set(
            array(
                'dos' => $dos,
                'status' => $status,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_dos'),
                'filter_method' => 'filter_dos',
                'do_statuses' => $this->mdl_dos->statuses()
            )
        );

        $this->layout->buffer('content', 'dos/index');
        $this->layout->render();
    }

    /**
     * @param $do_id
     */
    public function view($do_id)
    {
        $this->load->helper('custom_values');
        $this->load->model('mdl_do_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('units/mdl_units');
        $this->load->model('mdl_do_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_do_custom');

        $fields = $this->mdl_do_custom->by_id($do_id)->get()->result();
        $this->db->reset_query();

        $do_custom = $this->mdl_do_custom->where('do_id', $do_id)->get();

        if ($do_custom->num_rows()) {
            $do_custom = $do_custom->row();

            unset($do_custom->do_id, $do_custom->do_custom_id);

            foreach ($do_custom as $key => $val) {
                $this->mdl_dos->set_form_value('custom[' . $key . ']', $val);
            }
        }

        $do = $this->mdl_dos->get_by_id($do_id);


        if (!$do) {
            show_404();
        }

        $custom_fields = $this->mdl_custom_fields->by_table('ip_do_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->do_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_dos->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->do_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->layout->set(
            array(
                'do' => $do,
                'items' => $this->mdl_do_items->where('do_id', $do_id)->get()->result(),
                'do_id' => $do_id,
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'units' => $this->mdl_units->get()->result(),
                'do_tax_rates' => $this->mdl_do_tax_rates->where('do_id', $do_id)->get()->result(),
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'custom_js_vars' => array(
                    'currency_symbol' => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point' => get_setting('decimal_point')
                ),
                'do_statuses' => $this->mdl_dos->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array('modal_delete_do', 'dos/modal_delete_do'),
                array('modal_add_do_tax', 'dos/modal_add_do_tax'),
                array('content', 'dos/view')
            )
        );

        $this->layout->render();
    }

    /**
     * @param $do_id
     */
    public function delete($do_id)
    {
        // Delete the do
        $this->mdl_dos->delete($do_id);

        // Redirect to do index
        redirect('dos/index');
    }

    /**
     * @param $do_id
     * @param $item_id
     */
    public function delete_item($do_id, $item_id)
    {
        // Delete do item
        $this->load->model('mdl_do_items');
        $this->mdl_do_items->delete($item_id);

        // Redirect to do view
        redirect('dos/view/' . $do_id);
    }

    /**
     * @param $do_id
     * @param bool $stream
     * @param null $do_template
     */
    public function generate_pdf($do_id, $stream = true, $do_template = null)
    {
        $this->load->helper('pdf');

        if (get_setting('mark_dos_sent_pdf') == 1) {
            $this->mdl_dos->mark_sent($do_id);
        }

        generate_do_pdf($do_id, $stream, $do_template);
    }

    /**
     * @param $do_id
     * @param $do_tax_rate_id
     */
    public function delete_do_tax($do_id, $do_tax_rate_id)
    {
        $this->load->model('mdl_do_tax_rates');
        $this->mdl_do_tax_rates->delete($do_tax_rate_id);

        $this->load->model('mdl_do_amounts');
        $this->mdl_do_amounts->calculate($do_id);

        redirect('dos/view/' . $do_id);
    }

    public function recalculate_all_dos()
    {
        $this->db->select('do_id');
        $do_ids = $this->db->get('ip_dos')->result();

        $this->load->model('mdl_do_amounts');

        foreach ($do_ids as $do_id) {
            $this->mdl_do_amounts->calculate($do_id->do_id);
        }
    }

}
