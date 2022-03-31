<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function save()
    {
        $this->load->model('dos/mdl_dos_items');
        $this->load->model('dos/mdl_dos');
        $this->load->model('units/mdl_units');

        $dos_id = $this->input->post('dos_id');

        $this->mdl_dos->set_id($dos_id);

        if ($this->mdl_dos->run_validation('validation_rules_save_dos')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                if ($item->item_name) {
                    $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                    $item->item_price = ($item->item_quantity ? standardize_amount($item->item_price) : floatval(0));
                    $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);
                    $item->item_product_unit_id = ($item->item_product_unit_id ? $item->item_product_unit_id : null);
                    $item->item_product_unit = $this->mdl_units->get_name($item->item_product_unit_id, $item->item_quantity);

                    $item_id = ($item->item_id) ?: null;
                    unset($item->item_id);

                    $this->mdl_dos_items->save($item_id, $item);
                }
            }

            if ($this->input->post('dos_discount_amount') === '') {
                $dos_discount_amount = floatval(0);
            } else {
                $dos_discount_amount = $this->input->post('dos_discount_amount');
            }

            if ($this->input->post('dos_discount_percent') === '') {
                $dos_discount_percent = floatval(0);
            } else {
                $dos_discount_percent = $this->input->post('dos_discount_percent');
            }

            // Generate new dos number if needed
            $dos_number = $this->input->post('dos_number');
            $dos_status_id = $this->input->post('dos_status_id');

            if (empty($dos_number) && $dos_status_id != 1) {
                $dos_group_id = $this->mdl_dos->get_invoice_group_id($dos_id);
                $dos_number = $this->mdl_dos->get_dos_number($dos_group_id);
            }

            $db_array = array(
                'dos_number' => $dos_number,
                'dos_date_created' => date_to_mysql($this->input->post('dos_date_created')),
                'dos_date_expires' => date_to_mysql($this->input->post('dos_date_expires')),
                'dos_status_id' => $dos_status_id,
                'dos_password' => $this->input->post('dos_password'),
                'notes' => $this->input->post('notes'),
                'dos_discount_amount' => standardize_amount($dos_discount_amount),
                'dos_discount_percent' => standardize_amount($dos_discount_percent),
            );

            $this->mdl_dos->save($dos_id, $db_array);

            // Recalculate for discounts
            $this->load->model('dos/mdl_dos_amounts');
            $this->mdl_dos_amounts->calculate($dos_id);

            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }


        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = array();

            $values = [];
            foreach ($this->input->post('custom') as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }

            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }
            $this->load->model('custom_fields/mdl_dos_custom');
            $result = $this->mdl_dos_custom->save_custom($dos_id, $db_array);
            if ($result !== true) {
                $response = array(
                    'success' => 0,
                    'validation_errors' => $result
                );

                echo json_encode($response);
                exit;
            }
        }

        echo json_encode($response);
    }

    public function save_dos_tax_rate()
    {
        $this->load->model('dos/mdl_dos_tax_rates');

        if ($this->mdl_dos_tax_rates->run_validation()) {
            $this->mdl_dos_tax_rates->save();

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_dos_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    public function create()
    {
        $this->load->model('dos/mdl_dos');

        if ($this->mdl_dos->run_validation()) {
            $dos_id = $this->mdl_dos->create();

            $response = array(
                'success' => 1,
                'dos_id' => $dos_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function modal_change_client()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'client_id' => $this->input->post('client_id'),
            'dos_id' => $this->input->post('dos_id'),
            'clients' => $this->mdl_clients->get_latest(),
        );

        $this->layout->load_view('dos/modal_change_client', $data);
    }

    public function change_client()
    {
        $this->load->model('dos/mdl_dos');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_id = $this->input->post('client_id');
        $client = $this->mdl_clients->where('ip_clients.client_id', $client_id)
            ->get()->row();

        if (!empty($client)) {
            $dos_id = $this->input->post('dos_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('dos_id', $dos_id);
            $this->db->update('ip_dos', $db_array);

            $response = array(
                'success' => 1,
                'dos_id' => $dos_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function get_item()
    {
        $this->load->model('dos/mdl_dos_items');

        $item = $this->mdl_dos_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    public function modal_create_dos()
    {
        $this->load->module('layout');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'client' => $this->mdl_clients->get_by_id($this->input->post('client_id')),
            'clients' => $this->mdl_clients->get_latest(),
        );

        $this->layout->load_view('dos/modal_create_dos', $data);
    }

    public function modal_copy_dos()
    {
        $this->load->module('layout');

        $this->load->model('dos/mdl_dos');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'dos_id' => $this->input->post('dos_id'),
            'dos' => $this->mdl_dos->where('ip_dos.dos_id', $this->input->post('dos_id'))->get()->row(),
            'client' => $this->mdl_clients->get_by_id($this->input->post('client_id')),
        );

        $this->layout->load_view('dos/modal_copy_dos', $data);
    }

    public function copy_dos()
    {
        $this->load->model('dos/mdl_dos');
        $this->load->model('dos/mdl_dos_items');
        $this->load->model('dos/mdl_dos_tax_rates');

        if ($this->mdl_dos->run_validation()) {
            $target_id = $this->mdl_dos->save();
            $source_id = $this->input->post('dos_id');

            $this->mdl_dos->copy_dos($source_id, $target_id);

            $response = array(
                'success' => 1,
                'dos_id' => $target_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function modal_dos_to_invoice($dos_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('dos/mdl_dos');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'dos_id' => $dos_id,
            'dos' => $this->mdl_dos->where('ip_dos.dos_id', $dos_id)->get()->row()
        );

        $this->load->view('dos/modal_dos_to_invoice', $data);
    }

    public function dos_to_invoice()
    {
        $this->load->model(
            array(
                'invoices/mdl_invoices',
                'invoices/mdl_items',
                'dos/mdl_dos',
                'dos/mdl_dos_items',
                'invoices/mdl_invoice_tax_rates',
                'dos/mdl_dos_tax_rates'
            )
        );

        if ($this->mdl_invoices->run_validation()) {
            // Get the dos
            $dos = $this->mdl_dos->get_by_id($this->input->post('dos_id'));

            $invoice_id = $this->mdl_invoices->create(null, false);

            // Update the discounts
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_discount_amount', $dos->dos_discount_amount);
            $this->db->set('invoice_discount_percent', $dos->dos_discount_percent);
            $this->db->update('ip_invoices');

            // Save the invoice id to the dos
            $this->db->where('dos_id', $this->input->post('dos_id'));
            $this->db->set('invoice_id', $invoice_id);
            $this->db->update('ip_dos');

            $dos_items = $this->mdl_dos_items->where('dos_id', $this->input->post('dos_id'))->get()->result();

            foreach ($dos_items as $dos_item) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'item_tax_rate_id' => $dos_item->item_tax_rate_id,
                    'item_product_id' => $dos_item->item_product_id,
                    'item_name' => $dos_item->item_name,
                    'item_description' => $dos_item->item_description,
                    'item_quantity' => $dos_item->item_quantity,
                    'item_price' => $dos_item->item_price,
                    'item_product_unit_id' => $dos_item->item_product_unit_id,
                    'item_product_unit' => $dos_item->item_product_unit,
                    'item_discount_amount' => $dos_item->item_discount_amount,
                    'item_order' => $dos_item->item_order
                );

                $this->mdl_items->save(null, $db_array);
            }

            $dos_tax_rates = $this->mdl_dos_tax_rates->where('dos_id', $this->input->post('dos_id'))->get()->result();

            foreach ($dos_tax_rates as $dos_tax_rate) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => $dos_tax_rate->tax_rate_id,
                    'include_item_tax' => $dos_tax_rate->include_item_tax,
                    'invoice_tax_rate_amount' => $dos_tax_rate->dos_tax_rate_amount
                );

                $this->mdl_invoice_tax_rates->save(null, $db_array);
            }

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

}
