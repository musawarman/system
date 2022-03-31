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
 * Class Mdl_User_vendors
 */
class Mdl_User_Vendors extends MY_Model
{
    public $table = 'ip_user_vendors';
    public $primary_key = 'ip_user_vendors.user_vendor_id';

    public function default_select()
    {
        $this->db->select('ip_user_vendors.*, ip_users.user_name, ip_vendors.vendor_name, ip_vendors.vendor_surname');
    }

    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_vendors.user_id');
        $this->db->join('ip_vendors', 'ip_vendors.vendor_id = ip_user_vendors.vendor_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_vendors.vendor_name', 'ACS');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rules' => 'required'
            ),
            'vendor_id' => array(
                'field' => 'vendor_id',
                'label' => trans('vendor'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function assigned_to($user_id)
    {
        $this->filter_where('ip_user_vendors.user_id', $user_id);
        return $this;
    }
    
    /**
    * 
    * @param array $users_id
    */
    public function set_all_vendors_user($users_id)
    {
        $this->load->model('vendors/mdl_vendors');
        
        for ($x = 0; $x < count($users_id); $x++) {
            $vendors = $this->mdl_vendors->get_not_assigned_to_user($users_id[$x]);
            
            for ($i = 0; $i < count($vendors); $i++) {
                $user_vendor = array(
                    'user_id' => $users_id[$x],
                    'vendor_id' => $vendors[$i]->vendor_id
                );
                
                $this->db->insert('ip_user_vendors', $user_vendor);
            }
        }
    }
    
    public function get_users_all_vendors()
    {
        $this->load->model('users/mdl_users');
        $users = $this->mdl_users->where('user_all_vendors', 1)->get()->result();
        
        $new_users = array();
        
        for ($i = 0; $i < count($users); $i++) {
            array_push($new_users, $users[$i]->user_id);
        }
        
        $this->set_all_vendors_user($new_users);
    }
}
