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
 * Class Mdl_vendor_Notes
 */
class Mdl_vendor_Notes extends Response_Model
{
    public $table = 'ip_vendor_notes';
    public $primary_key = 'ip_vendor_notes.vendor_note_id';

    public function default_order_by()
    {
        $this->db->order_by('ip_vendor_notes.vendor_note_date DESC');
    }

    public function validation_rules()
    {
        return array(
            'vendor_id' => array(
                'field' => 'vendor_id',
                'label' => trans('vendor'),
                'rules' => 'required'
            ),
            'vendor_note' => array(
                'field' => 'vendor_note',
                'label' => trans('note'),
                'rules' => 'required'
            )
        );
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['vendor_note_date'] = date('Y-m-d');

        return $db_array;
    }

}
