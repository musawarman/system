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
 * Class Quotes
 */
class Quotes extends Admin_Controller
{
    /**
     * Quotes constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_quotes');
    }

    public function index($jns=null)
    {
        if($jns!=null)
            redirect('quotes/status/all_'.$jns);
        else
            redirect('quotes/status/all_out');
            // echo $jns;
        // Display all quotes by default
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of quotes to load
        if(strpos($status,'_')!==false)
            list($status,$jns)=explode('_',$status);
        else
        {
            $status=$status;
            $jns='0';
        }

        switch ($status) {
            case 'draft':
                $this->mdl_quotes->is_draft();
                break;
            case 'sent':
                $this->mdl_quotes->is_sent();
                break;
            case 'viewed':
                $this->mdl_quotes->is_viewed();
                break;
            case 'approved':
                $this->mdl_quotes->is_approved();
                break;
            case 'rejected':
                $this->mdl_quotes->is_rejected();
                break;
            case 'canceled':
                $this->mdl_quotes->is_canceled();
                break;
        }

        $this->mdl_quotes->where('ip_quotes.status_quotes',$jns)->paginate(site_url('quotes/status/' . $status), $page);
        $quotes = $this->mdl_quotes->result();

        $po=$this->db->from('ip_po')->where('po_status !=',-1)->get()->result();
        $po_s=array();
        foreach($po as $item)
        {
            $po_s[]=$item->quotes_id;
        }

        // print_r($po_s);

        $this->layout->set(
            array(
                'quotes' => $quotes,
                'status' => $status,
                'po_s' => $po_s,
                'jns' => $jns,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_quotes'),
                'filter_method' => 'filter_quotes',
                'quote_statuses' => $this->mdl_quotes->statuses()
            )
        );

        $this->layout->buffer('content', 'quotes/index');
        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function view($quote_id)
    {
        $this->load->helper('custom_values');
        $this->load->model('mdl_quote_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('units/mdl_units');
        $this->load->model('mdl_quote_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_quote_custom');

        $fields = $this->mdl_quote_custom->by_id($quote_id)->get()->result();
        $this->db->reset_query();

        $quote_custom = $this->mdl_quote_custom->where('quote_id', $quote_id)->get();

        if ($quote_custom->num_rows()) {
            $quote_custom = $quote_custom->row();

            unset($quote_custom->quote_id, $quote_custom->quote_custom_id);

            foreach ($quote_custom as $key => $val) {
                $this->mdl_quotes->set_form_value('custom[' . $key . ']', $val);
            }
        }

        $quote = $this->mdl_quotes->get_by_id($quote_id);


        if (!$quote) {
            show_404();
        }

        $custom_fields = $this->mdl_custom_fields->by_table('ip_quote_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->quote_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_quotes->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->quote_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        if($quote->vendor_id!=0)
        {
            $client_vendor_id=$quote->vendor_id;
            $vendor=$this->db->from('ip_vendors')->where('vendor_id',$quote->vendor_id)->get()->row();
        }
        else
        {
            $vendor=array();
            $client_vendor_id=$quote->client_id;
        }

        $this->layout->set(
            array(
                'quote' => $quote,
                'vendor' => $vendor,
                'client_vendor_id' => $client_vendor_id,
                'items' => $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
                'quote_id' => $quote_id,
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'units' => $this->mdl_units->get()->result(),
                'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'custom_js_vars' => array(
                    'currency_symbol' => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point' => get_setting('decimal_point')
                ),
                'quote_statuses' => $this->mdl_quotes->statuses()
            )
        );

        if($quote->vendor_id!=0){
            $this->layout->buffer(
                array(
                    array('modal_delete_quote', 'quotes/modal_delete_quote'),
                    array('modal_add_quote_tax', 'quotes/modal_add_quote_tax'),
                    array('content', 'quotes/view_vendor')
                )
            );
        }
        else
        {
            $this->layout->buffer(
                array(
                    array('modal_delete_quote', 'quotes/modal_delete_quote'),
                    array('modal_add_quote_tax', 'quotes/modal_add_quote_tax'),
                    array('content', 'quotes/view')
                )
            );
        }
        

        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function delete($quote_id)
    {
        // Delete the quote
        $cek=$this->db->query('select * from ip_quotes where quote_id="'.$quote_id.'"')->result();
        $jn=$cek[0]->status_quotes;
        $this->mdl_quotes->delete($quote_id);

        // Redirect to quote index
        redirect('quotes/index/'.$jn);
    }

    /**
     * @param $quote_id
     * @param $item_id
     */
    public function delete_item($quote_id, $item_id)
    {
        // Delete quote item
        $this->load->model('mdl_quote_items');
        $this->mdl_quote_items->delete($item_id);

        // Redirect to quote view
        redirect('quotes/view/' . $quote_id);
    }

    /**
     * @param $quote_id
     * @param bool $stream
     * @param null $quote_template
     */
    public function generate_pdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');

        if (get_setting('mark_quotes_sent_pdf') == 1) {
            $this->mdl_quotes->mark_sent($quote_id);
        }

        generate_quote_pdf($quote_id, $stream, $quote_template);
    }

    /**
     * @param $quote_id
     * @param $quote_tax_rate_id
     */
    public function delete_quote_tax($quote_id, $quote_tax_rate_id)
    {
        $this->load->model('mdl_quote_tax_rates');
        $this->mdl_quote_tax_rates->delete($quote_tax_rate_id);

        $this->load->model('mdl_quote_amounts');
        $this->mdl_quote_amounts->calculate($quote_id);

        redirect('quotes/view/' . $quote_id);
    }

    public function recalculate_all_quotes()
    {
        $this->db->select('quote_id');
        $quote_ids = $this->db->get('ip_quotes')->result();

        $this->load->model('mdl_quote_amounts');

        foreach ($quote_ids as $quote_id) {
            $this->mdl_quote_amounts->calculate($quote_id->quote_id);
        }
    }

    public function data_item($quote_id,$po=null)
    {
        $this->load->model('mdl_quote_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('mdl_quote_tax_rates');
        $quote = $this->mdl_quotes->get_by_id($quote_id);
        $data['quote']=$quote;
        $data['quote_id']=$quote_id;
        $data['items']=$this->mdl_quote_items->where('quote_id', $quote_id)->get()->result();
        $data['quote_tax_rates'] = $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result();
        if($po==null)
            $this->load->view('quotes/partial_item_table',$data);
        else
        {
            $this->load->view('quotes/data_item',$data);
        }
    }
   
    public function data_item_do($quote_id,$po=null)
    {
        $this->load->model('mdl_quote_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('mdl_quote_tax_rates');
        $quote = $this->mdl_quotes->get_by_id($quote_id);
        $data['quote']=$quote;
        $data['quote_id']=$quote_id;
        $data['items']=$this->mdl_quote_items->where('quote_id', $quote_id)->get()->result();
        $data['quote_tax_rates'] = $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result();
        
        $this->load->view('quotes/data_item_do',$data);
        
    }

    public function generate_po($id)
    {
        $d=$this->db->from('ip_quotes')->where('quote_id',$id)->get()->result();
        $jn=$d[0]->status_quotes;
        $this->load->model('mdl_settings');
        
        $po_st=$jn;
        if($jn=='in')
            $q_st='out';
        else
            $q_st='in';

        $jns=$q_st;

        $det=array();
        
        $data_po=$this->db->from('ip_po')->where('status_po',$jns)->get()->result();
        
        $d_po=array();
        foreach($data_po as $k=>$v)
        {
            $d_po[]=$v->quotes_id;
        }

        // $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->where('status_quotes',$q_st)->get()->result();
        $quotes=array();
        foreach($d as $item)
        {
            $quotes[$item->quote_id]=$item;
        }

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
        // print_r($det);
        $ponumber=$this->generate_po_number();

        $this->load->helper('country');
        $this->layout->set(
            array(
                'id' => $id,
                'quotes' => $quotes,
                'ponumber' => $ponumber,
                'suppliers' => $suppliers,
                'q_st' => $q_st,
                'd' => $d,
                'po_st' => $po_st,
                'vendors' => $vendors,
                'clients' => $clients,
                'd_po' => $d_po,
                'det' => $det,
                'jns' => $jns,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'po/form-quotes');
        $this->layout->render();
    }

    public function generate_po_number()
    {
        $thn=date('Y');
        $po=$this->db->query('select * from ip_po where YEAR(po_date)='.$thn.' and status_po="in" order by po_id desc limit 1')->result();
        $num=0;
        if(count($po)==0)
            $num=1;
        else
        {
            foreach($po as $item)
            {
                $n=strtok($item->po_number,'/');
                $num=$n+1;
            }
        }

        if($num<10)
            $num='00'.$num;
        elseif($n>=10 && $n<100)
            $num='0'.$num;
        else
            $num=$num;

        return $num.'/JDK-PO/'.romawi(date('n')).'/'.date('Y');
        // return $num.'/OSM-PO/'.romawi(date('n')).'/'.date('Y');

    }
    
}
