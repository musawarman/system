<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Supplier extends Admin_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('mdl_payments');
    }

    public function index($jns='active')
    {
        redirect('supplier/status/'.$jns);
    }

    public function status($status = 'active')
    {
        if($status=='active')
            $supplier=$this->db->from('ip_supplier')->where('supplier_active',1)->order_by('supplier_name')->get()->result();
        elseif($status=='inactive')
            $supplier=$this->db->from('ip_supplier')->where('supplier_active',0)->order_by('supplier_name')->get()->result();
        else
            $supplier=$this->db->from('ip_supplier')->where('supplier_active !=','-1')->order_by('supplier_name')->get()->result();

        
        
        $this->layout->set(
            array(
                'status' => $status,
                'jns' => $status,
                'supplier' => $supplier
            )
        );
        $this->layout->buffer('content', 'supplier/index');
        $this->layout->render();
        // $this->load->view('supplier/index',$data);
    }

    public function form($id = -1)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('supplier');
        }
        
        $new_client = false;
        
        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('supplier_name') != '') {
            $check = $this->db->get_where('ip_supplier', array(
                'supplier_name' => $this->input->post('supplier_name'),
                'supplier_surname' => $this->input->post('supplier_surname')
            ))->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('supplier_already_exists'));
                redirect('supplier/form');
            } else {
                $new_client = true;
            }
        }
        

        $this->load->helper('country');
        $this->load->helper('custom_values');

        $det=array();
        if($id!=-1)
            $det=$this->db->from('ip_supplier')->where('supplier_id',$id)->get()->result();

        $this->layout->set(
            array(
                'id' => $id,
                'det' => $det,
                'countries' => get_country_list(trans('cldr')),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'supplier/form');
        $this->layout->render();
    }

    public function proses($id='-1')
    {
        
        $input=$this->input->post();
        $in['supplier_active'] = $input['supplier_active'];
        $in['supplier_name'] = $input['supplier_name'];
        $in['supplier_surname'] = $input['supplier_surname'];
        $in['supplier_language'] = $input['supplier_language'];
        $in['supplier_contact_person_name'] = $input['supplier_contact_person_name'];
        $in['supplier_gender'] = $input['supplier_gender'];
        $in['supplier_birthdate'] = $input['supplier_birthdate'];
        $in['supplier_address_1'] = $input['supplier_address_1'];
        $in['supplier_address_2'] = $input['supplier_address_2'];
        $in['supplier_city'] = $input['supplier_city'];
        $in['supplier_state'] = $input['supplier_state'];
        $in['supplier_zip'] = $input['supplier_zip'];
        $in['supplier_country'] = $input['supplier_country'];
        $in['supplier_phone'] = $input['supplier_phone'];
        $in['supplier_fax'] = $input['supplier_fax'];
        $in['supplier_mobile'] = $input['supplier_mobile'];
        $in['supplier_email'] = $input['supplier_email'];
        $in['supplier_web'] = $input['supplier_web'];

        if($id=='-1')
        {
            $in['supplier_date_created']=date('Y-m-d H:i:s');
            $in['supplier_date_modified']=date('Y-m-d H:i:s');
            $c=$this->db->insert('ip_supplier',$in);
            if($c)
            {
                $this->session->set_flashdata('alert_success','Save New Data Supplier Successfull');
            }
            else
                $this->session->set_flashdata('alert_error','Save New Data Supplier Failed');
        }
        else
        {
            $in['supplier_date_modified']=date('Y-m-d H:i:s');
            $this->db->where('supplier_id',$id);
            $c=$this->db->update('ip_supplier',$in);
            
            if($c)
            {
                $this->session->set_flashdata('alert_success','Edit Data Supplier Successfull');
            }
            else
                $this->session->set_flashdata('alert_error','Edit Data Supplier Failed');
        }

        redirect('supplier/index','location');
        // echo '<pre>';
        // print_r($in);

    }

    public function delete($id)
    {
        $this->db->where('supplier_id',$id);
        $c=$this->db->update('ip_supplier',['supplier_active'=>'-1']);
        if($c)
        {
            $this->session->set_flashdata('alert_success','Delete Data Supplier Successfull');
        }
        else
            $this->session->set_flashdata('alert_error','Delete Data Supplier Failed');

        redirect('supplier/index','location');
    }
}