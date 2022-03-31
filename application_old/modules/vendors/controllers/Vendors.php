<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * jemiro
 */

/**
 * Class vendors
 */
class Vendors extends Admin_Controller
{
    /**
     * vendors constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_vendors');
    }

    public function index()
    {
        // Display active vendors by default
        redirect('vendors/status/active');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'active', $page = 0)
    {
        if (is_numeric(array_search($status, array('active', 'inactive')))) {
            $function = 'is_' . $status;
            $this->mdl_vendors->$function();
        }

        $this->mdl_vendors->with_total_balance()->paginate(site_url('vendors/status/' . $status), $page);
        $vendors = $this->mdl_vendors->result();

        $this->layout->set(
            array(
                'records' => $vendors,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_vendors'),
                'filter_method' => 'filter_vendors'
            )
        );

        $this->layout->buffer('content', 'vendors/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('vendors');
        }
        
        $new_vendor = false;
        
        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('vendor_name') != '') {
            $check = $this->db->get_where('ip_vendors', array(
                'vendor_name' => $this->input->post('vendor_name'),
                'vendor_surname' => $this->input->post('vendor_surname')
            ))->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('vendor_already_exists'));
                redirect('vendors/form');
            } else {
                $new_vendor = true;
            }
        }
        
        if ($this->mdl_vendors->run_validation()) {
            $id = $this->mdl_vendors->save($id);
            
            if ($new_vendor) {
                $this->load->model('user_vendors/mdl_user_vendors');
                $this->mdl_user_vendors->get_users_all_vendors();
            }
            
            $this->load->model('custom_fields/mdl_vendor_custom');
            $result = $this->mdl_vendor_custom->save_custom($id, $this->input->post('custom'));

            if ($result !== true) {
                $this->session->set_flashdata('alert_error', $result);
                $this->session->set_flashdata('alert_success', null);
                redirect('vendors/form/' . $id);
                return;
            } else {
                redirect('vendors/view/' . $id);
            }
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_vendors->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_vendor_custom');
            $this->mdl_vendors->set_form_value('is_update', true);

            $vendor_custom = $this->mdl_vendor_custom->where('vendor_id', $id)->get();

            if ($vendor_custom->num_rows()) {
                $vendor_custom = $vendor_custom->row();

                unset($vendor_custom->vendor_id, $vendor_custom->vendor_custom_id);

                foreach ($vendor_custom as $key => $val) {
                    $this->mdl_vendors->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_vendors->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_vendor_custom');

        $custom_fields = $this->mdl_custom_fields->by_table('ip_vendor_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_vendor_custom->get_by_clid($id);

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->vendor_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_vendors->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->vendor_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->load->helper('country');
        $this->load->helper('custom_values');

        $this->layout->set(
            array(
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'countries' => get_country_list(trans('cldr')),
                'selected_country' => $this->mdl_vendors->form_value('vendor_country') ?: get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'vendors/form');
        $this->layout->render();
    }

    /**
     * @param int $vendor_id
     */
    public function view($vendor_id)
    {
        $this->load->model('vendors/mdl_vendor_notes');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('payments/mdl_payments');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_vendor_custom');

        $vendor = $this->mdl_vendors
            ->with_total()
            ->with_total_balance()
            ->with_total_paid()
            ->where('ip_vendors.vendor_id', $vendor_id)
            ->get()->row();

        $custom_fields = $this->mdl_vendor_custom->get_by_vendor($vendor_id)->result();

        $this->mdl_vendor_custom->prep_form($vendor_id);

        if (!$vendor) {
            show_404();
        }



        $this->layout->set(
            array(
                'vendor' => $vendor,
                'vendor_notes' => $this->mdl_vendor_notes->where('vendor_id', $vendor_id)->get()->result(),
                'invoices' => $this->mdl_invoices->by_vendor($vendor_id)->limit(20)->get()->result(),
                'quotes' => $this->mdl_quotes->by_vendor($vendor_id)->limit(20)->get()->result(),
                // 'payments' => $this->mdl_payments->by_vendor($vendor_id)->limit(20)->get()->result(),
                // 'payments' => $pay->limit(20)->get()->result(),
                'custom_fields' => $custom_fields,
                'quote_statuses' => $this->mdl_quotes->statuses(),
                'invoice_statuses' => $this->mdl_invoices->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array(
                    'invoice_table',
                    'invoices/partial_invoice_table'
                ),
                array(
                    'quote_table',
                    'quotes/partial_quote_table'
                ),
                array(
                    'payment_table',
                    'vendors/partial_vendor_payment'
                ),
                array(
                    'partial_notes',
                    'vendors/partial_notes'
                ),
                array(
                    'content',
                    'vendors/view'
                )
            )
        );

        $this->layout->render();
    }

    /**
     * @param int $vendor_id
     */
    public function delete($vendor_id)
    {
        $this->mdl_vendors->delete($vendor_id);
        redirect('vendors');
    }

}
