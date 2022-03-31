<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Grafik extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('date');
        $this->load->helper(array('form', 'url'));
    }

    public function index()
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        $this->layout->set(
            array(
                
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'grafik/index');
        $this->layout->render();
    }

    public function data($bln,$thn)
    {
        $data['bulan']=$bln;
        $data['tahun']=$thn;

        $quotes=$this->db->from('ip_quotes')->where('MONTH(quote_date_created)',$bln)->where('YEAR(quote_date_created)',$thn)->get()->result();
        $datapo=$this->db->from('ip_po')->where('po_status!=','-1')->where('MONTH(po_date)',$bln)->where('YEAR(po_date)',$thn)->get()->result();
        $invoices=$this->db->from('ip_invoices')->where('po_id!=',0)->where('MONTH(invoice_date_created)',$bln)->where('YEAR(invoice_date_created)',$thn)->get()->result();
        $payment=$this->db->from('ip_payments')->where('MONTH(payment_date)',$bln)->where('YEAR(payment_date)',$thn)->get()->result();

        $quote=$po=$invoice=$payment=array();

        $q_in=$q_out=0;
        foreach($quotes as $k)
        {
            if($k->status_quotes=='in')
                $q_in++;
            else
                $q_out++;
        }
        $data['quote_in']=$q_in;
        $data['quote_out']=$q_out;

        $po_in=$po_out=0;
        foreach($datapo as $k)
        {
            if($k->status_po=='in')
                $po_in++;
            else
                $po_out++;
        }
        $data['po_in']=$po_in;
        $data['po_out']=$po_out;

        $inv_in=$inv_out=0;
        foreach($invoices as $k)
        {
            if($k->status_invoices=='in')
                $inv_in++;
            else
                $inv_out++;
        }
        $data['inv_in']=$inv_in;
        $data['inv_out']=$inv_out;

        $data['quote']=$quote;
        $data['po']=$po;
        $data['invoice']=$invoice;
        $this->load->view('grafik/grafik',$data);
    }
}