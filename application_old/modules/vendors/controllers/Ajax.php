<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function name_query()
    {
        // Load the model & helper
        $this->load->model('vendors/mdl_vendors');

        $response = array();

        // Get the post input
        $query = $this->input->get('query');
        $permissiveSearchVendors = $this->input->get('permissive_search_vendors');

        if (empty($query)) {
            echo json_encode($response);
            exit;
        }

        // Search for chars "in the middle" of vendors names
        $permissiveSearchVendors ? $moreVendorsQuery = '%' : $moreVendorsQuery = '';

        // Search for vendors
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace("%", "", $escapedQuery);
        $vendors = $this->mdl_vendors
            ->where('vendor_active', 1)
            ->having('vendor_name LIKE \'' . $moreVendorsQuery . $escapedQuery . '%\'')
            ->or_having('vendor_surname LIKE \'' . $moreVendorsQuery . $escapedQuery . '%\'')
            ->or_having('vendor_fullname LIKE \'' . $moreVendorsQuery . $escapedQuery . '%\'')
            ->order_by('vendor_name')
            ->get()
            ->result();

        foreach ($vendors as $vendor) {
            $response[] = array(
                'id' => $vendor->vendor_id,
                'text' => htmlsc(format_vendor($vendor)),
            );
        }

        // Return the results
        echo json_encode($response);
    }

    public function save_preference_permissive_search_vendors()
    {
        $this->load->model('mdl_settings');
        $permissiveSearchVendors = $this->input->get('permissive_search_vendors');

        if (!preg_match('!^[0-1]{1}$!', $permissiveSearchVendors)) {
            exit;
        }

        $this->mdl_settings->save('enable_permissive_search_vendors', $permissiveSearchVendors);
    }

    public function save_vendor_note()
    {
        $this->load->model('vendors/mdl_vendor_notes');

        if ($this->mdl_vendor_notes->run_validation()) {
            $this->mdl_vendor_notes->save();

            $response = array(
                'success' => 1,
                'new_token' => $this->security->get_csrf_hash(),
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'new_token' => $this->security->get_csrf_hash(),
                'validation_errors' => json_errors(),
            );
        }

        echo json_encode($response);
    }

    public function load_vendor_notes()
    {
        $this->load->model('vendors/mdl_vendor_notes');
        $data = array(
            'vendor_notes' => $this->mdl_vendor_notes->where('vendor_id',
                $this->input->post('vendor_id'))->get()->result()
        );

        $this->layout->load_view('vendors/partial_notes', $data);
    }

}
