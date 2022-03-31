<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Jurnal extends Admin_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('date');
        $this->load->helper(array('form', 'url'));
        // $this->load->model('mdl_payments');
    }

    public function index()
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        $data_po=array();
        
        $invoices=$this->db->from('ip_invoices')->get()->result();
        $invoice=$dinvoice=array();
        foreach($invoices as $k)
        {
            $dinvoice[$k->invoice_id]=$k;
            $invoice[]=$k->invoice_id;
        }

        $dorder=$this->db->from('ip_do')->where_in('invoice_id',$invoice)->get()->result();

        $data_po=$this->db->from('ip_po')->where('po_status!=',-1)->order_by('po_date','desc')->get()->result();
        
        $vendor=$this->db->from('ip_vendors')->get()->result();
        $vendors=array();
        foreach($vendor as $item)
        {
            $vendors[$item->vendor_id]=$item;
        }
        $client=$this->db->from('ip_clients')->get()->result();
        $clients=array();
        foreach($client as $item)
        {
            $clients[$item->client_id]=$item;
        }
        
        $supplier=$this->db->from('ip_supplier')->get()->result();
        $suppliers=array();
        foreach($supplier as $item)
        {
            $suppliers[$item->supplier_id]=$item;
        }

        $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->get()->result();
        $quotes=array();
        foreach($quote as $item)
        {
            $quotes[$item->quote_id]=$item;
        }

        $amount=$this->db->from('ip_quote_amounts')->get()->result();
        $amounts=array();
        foreach($amount as $item)
        {
            $amounts[$item->quote_id][]=$item;
        }

        $this->layout->set(
            array(
                'data_po' => $data_po,
                'quotes' => $quotes,
                'dorder' => $dorder,
                'dinvoice' => $dinvoice,
                'amounts' => $amounts,
                'suppliers' => $suppliers,
                'vendors' => $vendors,
                'clients' => $clients,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'jurnal/index');
        $this->layout->render();
    }

    public function data($bln,$thn)
    {
        $invoices=$this->db->from('ip_invoices')->where('MONTH(invoice_date_created)',$bln)->where('YEAR(invoice_date_created)',$thn)->get()->result();
        $invoice=$invoice_id=array();
        foreach($invoices as $k=>$v)
        {
            $invoice[$v->invoice_id]=$v;
            $invoice_id[]=$v->invoice_id;
        }

        $payments=$this->db->from('ip_payments')->where('MONTH(payment_date)',$bln)->where('YEAR(payment_date)',$thn)->order_by('payment_date')->get()->result();
        $payment=array();
        foreach($payments as $k=>$v)
        {
            $payment[$v->invoice_id][]=$v;
        }
        // echo '<pre>';
        // print_r($payment);
        // echo '</pre>';
        $data['payment']=$payment;
        $data['invoice']=$invoice;
        $data['invoice_id']=$invoice_id;
        $this->load->view('jurnal/data',$data);
    }

    public function excel($bulan,$tahun)
    {
        $invoices=$this->db->from('ip_invoices')->where('YEAR(invoice_date_created)',$tahun)->get()->result();
        $invoice=$invoice_id=array();
        foreach($invoices as $k=>$v)
        {
            $invoice[$v->invoice_id]=$v;
            $invoice_id[]=$v->invoice_id;
        }

        $payments=$this->db->from('ip_payments')->where('MONTH(payment_date)',$bulan)->where('YEAR(payment_date)',$tahun)->order_by('payment_date')->get()->result();
        $payment=array();
        foreach($payments as $k=>$v)
        {
            $payment[$v->invoice_id][]=$v;
        }
        $data['payment']=$payment;
        $data['invoice']=$invoice;
        $data['invoice_id']=$invoice_id;
        $data['bulan']=$bulan;
        $data['tahun']=$tahun;
        $this->load->view('jurnal/excel',$data);
    }
}