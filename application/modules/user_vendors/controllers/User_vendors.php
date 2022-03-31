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
 * Class User_vendors
 */
class User_Vendors extends Admin_Controller
{
    /**
     * Custom_Values constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('users/mdl_users');
        $this->load->model('vendors/mdl_vendors');
        $this->load->model('user_vendors/mdl_user_vendors');
    }

    public function index()
    {
        redirect('users');
    }

    /**
     * @param null $id
     */
    public function user($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        $user = $this->mdl_users->get_by_id($id);

        if (empty($user)) {
            redirect('users');
        }

        $user_vendors = $this->mdl_user_vendors->assigned_to($id)->get()->result();

        $this->layout->set('user', $user);
        $this->layout->set('user_vendors', $user_vendors);
        $this->layout->set('id', $id);
        $this->layout->buffer('content', 'user_vendors/field');
        $this->layout->render();
    }

    /**
     * @param null $user_id
     */
    public function create($user_id = null)
    {
        if (!$user_id) {
            redirect('custom_values');
        }

        if ($this->input->post('btn_cancel')) {
            redirect('user_vendors/field/' . $user_id);
        }

        if ($this->mdl_user_vendors->run_validation()) {
            
            if ($this->input->post('user_all_vendors')) {
                $users_id = array($user_id);
                
                $this->mdl_user_vendors->set_all_vendors_user($users_id);
                
                $user_update = array(
                    'user_all_vendors' => 1
                );
                
            } else {
                $user_update = array(
                    'user_all_vendors' => 0
                );
                
               $this->mdl_user_vendors->save(); 
            }
            
            $this->db->where('user_id',$user_id);
            $this->db->update('ip_users',$user_update);
            
            redirect('user_vendors/user/' . $user_id);
        }

        $user = $this->mdl_users->get_by_id($user_id);
        $vendors = $this->mdl_vendors->get_not_assigned_to_user($user_id);

        $this->layout->set('id', $user_id);
        $this->layout->set('user', $user);
        $this->layout->set('vendors', $vendors);
        $this->layout->buffer('content', 'user_vendors/new');
        $this->layout->render();
    }

    /**
     * @param integer $user_vedor_id
     */
    public function delete($user_vedor_id)
    {
        $ref = $this->mdl_user_vendors->get_by_id($user_vedor_id);

        $this->mdl_user_vendors->delete($user_vedor_id);
        redirect('user_vendors/user/' . $ref->user_id);
    }

}
