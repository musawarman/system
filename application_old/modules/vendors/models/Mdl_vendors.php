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
 * Class Mdl_vendors
 */
class Mdl_vendors extends Response_Model
{
    public $table = 'ip_vendors';
    public $primary_key = 'ip_vendors.vendor_id';
    public $date_created_field = 'vendor_date_created';
    public $date_modified_field = 'vendor_date_modified';

    public function default_select()
    {
        $this->db->select(
            'SQL_CALC_FOUND_ROWS ' . $this->table . '.*, ' .
            'CONCAT(' . $this->table . '.vendor_name, " ", ' . $this->table . '.vendor_surname) as vendor_fullname'
            , false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_vendors.vendor_name');
    }

    public function validation_rules()
    {
        return array(
            'vendor_name' => array(
                'field' => 'vendor_name',
                'label' => trans('vendor_name'),
                'rules' => 'required'
            ),
            'vendor_surname' => array(
                'field' => 'vendor_surname',
                'label' => trans('vendor_surname')
            ),
            'vendor_active' => array(
                'field' => 'vendor_active'
            ),
            'vendor_language' => array(
                'field' => 'vendor_language',
                'label' => trans('language'),
            ),
            'vendor_address_1' => array(
                'field' => 'vendor_address_1'
            ),
            'vendor_address_2' => array(
                'field' => 'vendor_address_2'
            ),
            'vendor_city' => array(
                'field' => 'vendor_city'
            ),
            'vendor_state' => array(
                'field' => 'vendor_state'
            ),
            'vendor_zip' => array(
                'field' => 'vendor_zip'
            ),
            'vendor_country' => array(
                'field' => 'vendor_country'
            ),
            'vendor_phone' => array(
                'field' => 'vendor_phone'
            ),
            'vendor_fax' => array(
                'field' => 'vendor_fax'
            ),
            'vendor_mobile' => array(
                'field' => 'vendor_mobile'
            ),
            'vendor_email' => array(
                'field' => 'vendor_email'
            ),
            'vendor_web' => array(
                'field' => 'vendor_web'
            ),
            'vendor_vat_id' => array(
                'field' => 'user_vat_id'
            ),
            'vendor_tax_code' => array(
                'field' => 'user_tax_code'
            ),
            // SUMEX
            'vendor_birthdate' => array(
                'field' => 'vendor_birthdate',
                'rules' => 'callback_convert_date'
            ),
            'vendor_gender' => array(
                'field' => 'vendor_gender'
            ),
            'vendor_avs' => array(
                'field' => 'vendor_avs',
                'label' => trans('sumex_ssn'),
                'rules' => 'callback_fix_avs'
            ),
            'vendor_insurednumber' => array(
                'field' => 'vendor_insurednumber',
                'label' => trans('sumex_insurednumber')
            ),
            'vendor_veka' => array(
                'field' => 'vendor_veka',
                'label' => trans('sumex_veka')
            ),
        );
    }

    /**
     * @param int $amount
     * @return mixed
     */
    function get_latest($amount = 10)
    {
        return $this->mdl_vendors
            ->where('vendor_active', 1)
            ->order_by('vendor_id', 'DESC')
            ->limit($amount)
            ->get()
            ->result();
    }

    /**
     * @param $input
     * @return string
     */
    function fix_avs($input)
    {
        if ($input != "") {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            } else if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }

        return "";
    }

    function convert_date($input)
    {
        $this->load->helper('date_helper');

        if ($input == '') {
            return '';
        }

        return date_to_mysql($input);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if (!isset($db_array['vendor_active'])) {
            $db_array['vendor_active'] = 0;
        }

        return $db_array;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns vendor_id of existing vendor
     *
     * @param $vendor_name
     * @return int|null
     */
    public function vendor_lookup($vendor_name)
    {
        $vendor = $this->mdl_vendors->where('vendor_name', $vendor_name)->get();

        if ($vendor->num_rows()) {
            $vendor_id = $vendor->row()->vendor_id;
        } else {
            $db_array = array(
                'vendor_name' => $vendor_name
            );

            $vendor_id = parent::save(null, $db_array);
        }

        return $vendor_id;
    }

    public function with_total()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.vendor_id = ip_vendors.vendor_id)), 0) AS vendor_invoice_total', false);
        return $this;
    }

    public function with_total_paid()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_paid) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.vendor_id = ip_vendors.vendor_id)), 0) AS vendor_invoice_paid', false);
        return $this;
    }

    public function with_total_balance()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_balance) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.vendor_id = ip_vendors.vendor_id)), 0) AS vendor_invoice_balance', false);
        return $this;
    }

    public function is_inactive()
    {
        $this->filter_where('vendor_active', 0);
        return $this;
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function get_not_assigned_to_user($user_id)
    {
        $this->load->model('user_vendors/mdl_user_vendors');
        $vendors = $this->mdl_user_vendors->select('ip_user_vendors.vendor_id')
            ->assigned_to($user_id)->get()->result();

        $assigned_vendors = [];
        foreach ($vendors as $vendor) {
            $assigned_Vendors[] = $vendor->vendor_id;
        }

        if (count($assigned_vendors) > 0) {
            $this->where_not_in('ip_vendors.vendor_id', $assigned_vendors);
        }

        $this->is_active();
        return $this->get()->result();
    }

    public function is_active()
    {
        $this->filter_where('vendor_active', 1);
        return $this;
    }

}
