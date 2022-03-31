<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends Admin_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('date');
        $this->load->helper(array('form', 'url'));
    }

    public function formnew($jns)
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        
        if($jns=='in')
            $st_po='out';
        else
            $st_po='in';

        $det=array(); 
        $det=$data_po=$this->db->from('ip_po')->where('status_po',$st_po)->where('po_status !=',-1)->get()->result();
        $data['jns']=$jns;
        
        $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->get()->result();
        $quotes=array();
        foreach($quote as $item)
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

        $getpaid=$this->db->query('select distinct(inv.po_id) as po_id from ip_invoices as inv inner join ip_po as pay on (pay.po_id=inv.po_id)')->result();
        $paid=array();
        foreach($getpaid as $item)
        {
            $paid[]=$item->po_id;
        }

        // echo '<pre>';
        // print_r($paid);
        // echo '</pre>';
        $this->layout->set(
            array(
                'data_po' => $data_po,
                'det' => $det,
                'paid' => $paid,
                'jns' => $jns,
                'quotes' => $quotes,
                'suppliers' => $suppliers,
                'vendors' => $vendors,
                'clients' => $clients,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        
        $this->layout->buffer('content', 'invoice/form-new');
            
        $this->layout->render();
    }

    public function form($idpo=-1)
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        
        $det=array();
        
        if($idpo!=-1)
        {
            $det=$data_po=$this->db->from('ip_po')->where('po_id',$idpo)->get()->result();
            $jns=$det[0]->status_po;
        }
        else
            $jns='in';

        $st_po=$jns;
        if($jns=='in')
            $data['jns']=$jns='out';
        else
            $data['jns']=$jns='in';

        $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->get()->result();
        $quotes=array();
        foreach($quote as $item)
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

        
        $this->layout->set(
            array(
                'idpo' => $idpo,
                'data_po' => $data_po,
                'det' => $det,
                'st_po' => $st_po,
                'jns' => $jns,
                'quotes' => $quotes,
                'suppliers' => $suppliers,
                'vendors' => $vendors,
                'clients' => $clients,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        if($jns=='out')
            $this->layout->buffer('content', 'invoice/form');
        else
            $this->layout->buffer('content', 'invoice/form-in');
            
        $this->layout->render();
    }

    public function prosesin($idpo=-1)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $data=$this->input->post();

        list($po_id,$quote_id,$name_client,$id_client)=explode('__',$data['po_number']);


        $quotes=$this->db->from('ip_quotes')->where('quote_id',$quote_id)->get()->row();
        if(count($quotes)!=0)
        {
            $ins['vendor_id']=$quotes->vendor_id;
        }


        if (!empty($_FILES['do_file']['name']))
        {
            $config = array(
                'upload_path' => "./uploads/import/",
                'allowed_types' => "gif|jpg|png|jpeg|pdf",
                'overwrite' => TRUE,
                'max_size' => "4096000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "2048",
                'max_width' => "2048"
            );
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('do_file'))
            {
                $error = array('error' => $this->upload->display_errors());
                $ins['do_file']='';
            }
            else
            {
                $upl=$this->upload->data();
                $nama_file=$upl['file_name'];
                $upload = array('upload_data' => $upl);
                $ins['do_file']=$nama_file;
            }
        }
        else
        {
            $ins['do_file']='';
        }

        $ins['po_id']=$po_id;
        $ins['quote_id']=$quote_id;
        $ins['status_quotes']=$data['status_quotes'];
        $ins['status_invoices']=$data['status_invoices'];
        $ins['invoice_date_created']=$invoice_date_created=date('Y-m-d',strtotime($data['invoice_date_created']));
        $ins['invoice_terms'] = get_setting('default_invoice_terms');
        $ins['invoice_date_due']=$this->get_date_due($invoice_date_created);
        $ins['client_id']=$data['client_id'];
        $ins['invoice_time_created']=date('H:i:s');
        $ins['invoice_date_modified']=$invoice_date_created.' '.date('H:i:s');
        $ins['do_number']=$data['do_number'];
        $generate_invoice_number = get_setting('generate_invoice_number_for_draft');

        if (!isset($data['invoice_status_id'])) {
            $data['invoice_status_id'] = 1;
            $ins['invoice_status_id'] = 1;
        }

        if ($data['invoice_status_id'] === 1 && $generate_invoice_number == 1) {
            $ins['invoice_number'] = $this->get_invoice_number($data['invoice_group_id']);
        } elseif ($data['invoice_status_id'] != 1) {
            $ins['invoice_number'] = $this->get_invoice_number($data['invoice_group_id']);
        } else {
            $ins['invoice_number'] = '';
        }
        $ins['invoice_status_id']=1;
        $ins['invoice_password']=$data['invoice_password'];
        $ins['invoice_url_key'] = $this->get_url_key();
        $ins['invoice_group_id'] = $data['invoice_group_id'];
        $ins['user_id'] = $this->session->userdata('user_id');
        
        $this->db->insert('ip_invoices',$ins);
        $invoice_id=$this->db->insert_id();

        $inv = $this->db->from('ip_invoices')->where('invoice_id', $invoice_id)->get()->row();
        $invoice_group = $data['invoice_group_id'];

        // Create an invoice amount record
        $db_array = array(
            'invoice_id' => $invoice_id
        );

        
        $invgroup = $this->mdl_invoice_groups->where('invoice_group_id', $invoice_group)->get()->row();
        if (preg_match("/sumex/i", $invgroup->invoice_group_name)) {
            // If the Invoice Group includes "Sumex", make the invoice a Sumex one
            $db_array = array(
                'sumex_invoice' => $invoice_id
            );
            $this->db->insert('ip_invoice_sumex', $db_array);
        }
        
        // echo $invoice_id;

        //INSERT ITEM START
            $quote_amount=$this->db->from('ip_quote_amounts')->where('quote_id',$quote_id)->get()->row();
            $inv_amount['invoice_id']=$invoice_id;
            $inv_amount['invoice_sign']=1;
            $inv_amount['invoice_item_subtotal']=$quote_amount->quote_item_subtotal;
            $inv_amount['invoice_item_tax_total']=$quote_amount->quote_item_tax_total;
            $inv_amount['invoice_tax_total']=$quote_amount->quote_tax_total;
            $inv_amount['invoice_total']=$quote_amount->quote_total;
            $inv_amount['invoice_paid']=0;
            $inv_amount['invoice_balance']=$quote_amount->quote_total;
            $this->db->insert('ip_invoice_amounts', $inv_amount);

            $quote_tax_rate=$this->db->from('ip_quote_tax_rates')->where('quote_id',$quote_id)->get()->row();
            $inv_tax_rate['invoice_id']=$invoice_id;
            //$inv_tax_rate['invoice_tax_rate_id']=$quote_tax_rate->quote_tax_rate_id;
            $inv_tax_rate['tax_rate_id']=$quote_tax_rate->tax_rate_id;
            $inv_tax_rate['include_item_tax']=$quote_tax_rate->include_item_tax;
            $inv_tax_rate['invoice_tax_rate_amount']=$quote_tax_rate->quote_tax_rate_amount;
            $this->db->insert('ip_invoice_tax_rates', $inv_tax_rate);


            $quote_items=$this->db->from('ip_quote_items')->where('quote_id',$quote_id)->get()->result();
            foreach($quote_items as $quote_item)
            {
                $inv_items['invoice_id']=$invoice_id;
                $inv_items['item_tax_rate_id']=$quote_item->item_tax_rate_id;
                $inv_items['item_product_id']=$quote_item->item_product_id;
                $inv_items['item_date_added']=$quote_item->item_date_added;
                $inv_items['item_name']=$quote_item->item_name;
                $inv_items['item_description']=$quote_item->item_description;
                $inv_items['item_quantity']=$quote_item->item_quantity;
                $inv_items['item_price']=$quote_item->item_price;
                $inv_items['item_discount_amount']=$quote_item->item_discount_amount;
                $inv_items['item_order']=$quote_item->item_order;
                $inv_items['item_product_unit']=$quote_item->item_product_unit;
                $inv_items['item_product_unit_id']=$quote_item->item_product_unit_id;
                $this->db->insert('ip_invoice_items', $inv_items);
                $item_id=$this->db->insert_id();

                $quote_itemid=$quote_item->item_id;
                $item_amounts=$this->db->from('ip_quote_item_amounts')->where('item_id',$quote_itemid)->get()->row();
                $inv_item['item_id']=$item_id;
                $inv_item['item_subtotal']=$item_amounts->item_subtotal;
                $inv_item['item_tax_total']=$item_amounts->item_tax_total;
                $inv_item['item_discount']=$item_amounts->item_discount;
                $inv_item['item_total']=$item_amounts->item_total;
                $this->db->insert('ip_invoice_item_amounts',$inv_item);
            }

        $up_po['po_status']='1';
        $up_po['po_date_modified']=date('Y-m-d H:i:s');
        $this->db->where('po_id',$po_id);
        $this->db->update('ip_po',$up_po);
        //INSERT ITEM END
        redirect('invoices/view/'.$invoice_id,'location');
        
    }
    public function proses($idpo=-1)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $data=$this->input->post();

        list($po_id,$quote_id,$name_client,$id_client)=explode('__',$data['po_number']);

        $quotes=$this->db->from('ip_quotes')->where('quote_id',$quote_id)->get()->row();
        if(count($quotes)!=0)
        {
            $ins['vendor_id']=$quotes->vendor_id;
        }

        $ins['po_id']=$po_id;
        $ins['quote_id']=$quote_id;
        $ins['status_quotes']=$data['status_quotes'];
        $ins['status_invoices']=$data['status_invoices'];
        $ins['invoice_date_created']=$invoice_date_created=date('Y-m-d',strtotime($data['invoice_date_created']));
        $ins['invoice_terms'] = get_setting('default_invoice_terms');
        $ins['invoice_date_due']=$this->get_date_due($invoice_date_created);
        $ins['client_id']=$data['client_id'];
        $ins['invoice_time_created']=date('H:i:s');
        $ins['invoice_date_modified']=$invoice_date_created.' '.date('H:i:s');
        $generate_invoice_number = get_setting('generate_invoice_number_for_draft');

        if (!isset($data['invoice_status_id'])) {
            $data['invoice_status_id'] = 1;
            $ins['invoice_status_id'] = 1;
        }

        if ($data['invoice_status_id'] === 1 && $generate_invoice_number == 1) {
            $ins['invoice_number'] = $this->get_invoice_number($data['invoice_group_id']);
        } elseif ($data['invoice_status_id'] != 1) {
            $ins['invoice_number'] = $this->get_invoice_number($data['invoice_group_id']);
        } else {
            $ins['invoice_number'] = '';
        }
        $ins['invoice_status_id']=1;
        $ins['invoice_password']=$data['invoice_password'];
        $ins['invoice_url_key'] = $this->get_url_key();
        $ins['invoice_group_id'] = $data['invoice_group_id'];
        $ins['user_id'] = $this->session->userdata('user_id');

        if($data['do_number']!='')
            $ins['do_number'] = $data['do_number'];
        else
            $ins['do_number'] = $this->generate_do_number();

        $ins['no_kwitansi']=$this->generate_kw_number();

        if (!empty($_FILES['do_file']['name']))
        {
            $config = array(
                'upload_path' => "./uploads/import/",
                'allowed_types' => "gif|jpg|png|jpeg|pdf",
                'overwrite' => TRUE,
                'max_size' => "4096000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                'max_height' => "2048",
                'max_width' => "2048"
            );
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('do_file'))
            {
                $error = array('error' => $this->upload->display_errors());
                $ins['do_file']='';
            }
            else
            {
                $upl=$this->upload->data();
                $nama_file=$upl['file_name'];
                $upload = array('upload_data' => $upl);
                $ins['do_file']=$nama_file;
            }
        }
        else
        {
            $ins['do_file']='';
        }

        // echo $nama_file;
        // exit();
        $this->db->insert('ip_invoices',$ins);
        $invoice_id=$this->db->insert_id();

        $inv = $this->db->from('ip_invoices')->where('invoice_id', $invoice_id)->get()->row();
        $invoice_group = $data['invoice_group_id'];

        // Create an invoice amount record
        $db_array = array(
            'invoice_id' => $invoice_id
        );

        
        $invgroup = $this->mdl_invoice_groups->where('invoice_group_id', $invoice_group)->get()->row();
        if (preg_match("/sumex/i", $invgroup->invoice_group_name)) {
            // If the Invoice Group includes "Sumex", make the invoice a Sumex one
            $db_array = array(
                'sumex_invoice' => $invoice_id
            );
            $this->db->insert('ip_invoice_sumex', $db_array);
        }
        
        // echo $invoice_id;

        //INSERT ITEM START
            $quote_amount=$this->db->from('ip_quote_amounts')->where('quote_id',$quote_id)->get()->row();
            $inv_amount['invoice_id']=$invoice_id;
            $inv_amount['invoice_sign']=1;
            $inv_amount['invoice_item_subtotal']=$quote_amount->quote_item_subtotal;
            $inv_amount['invoice_item_tax_total']=$quote_amount->quote_item_tax_total;
            $inv_amount['invoice_tax_total']=$quote_amount->quote_tax_total;
            $inv_amount['invoice_total']=$quote_amount->quote_total;
            $inv_amount['invoice_paid']=0;
            $inv_amount['invoice_balance']=$quote_amount->quote_total;
            $this->db->insert('ip_invoice_amounts', $inv_amount);

            $quote_tax_rate=$this->db->from('ip_quote_tax_rates')->where('quote_id',$quote_id)->get()->row();
            $inv_tax_rate['invoice_id']=$invoice_id;
            $inv_tax_rate['tax_rate_id']=$quote_tax_rate->tax_rate_id;
            $inv_tax_rate['include_item_tax']=$quote_tax_rate->include_item_tax;
            $inv_tax_rate['invoice_tax_rate_amount']=$quote_tax_rate->quote_tax_rate_amount;
            $this->db->insert('ip_invoice_tax_rates', $inv_tax_rate);


            $quote_items=$this->db->from('ip_quote_items')->where('quote_id',$quote_id)->get()->result();
            foreach($quote_items as $quote_item)
            {
                $inv_items['invoice_id']=$invoice_id;
                $inv_items['item_tax_rate_id']=$quote_item->item_tax_rate_id;
                $inv_items['item_product_id']=$quote_item->item_product_id;
                $inv_items['item_date_added']=$quote_item->item_date_added;
                $inv_items['item_name']=$quote_item->item_name;
                $inv_items['item_description']=$quote_item->item_description;
                $inv_items['item_quantity']=$quote_item->item_quantity;
                $inv_items['item_price']=$quote_item->item_price;
                $inv_items['item_discount_amount']=$quote_item->item_discount_amount;
                $inv_items['item_order']=$quote_item->item_order;
                $inv_items['item_product_unit']=$quote_item->item_product_unit;
                $inv_items['item_product_unit_id']=$quote_item->item_product_unit_id;
                $this->db->insert('ip_invoice_items', $inv_items);
                $item_id=$this->db->insert_id();

                $quote_itemid=$quote_item->item_id;
                $item_amounts=$this->db->from('ip_quote_item_amounts')->where('item_id',$quote_itemid)->get()->row();
                $inv_item['item_id']=$item_id;
                $inv_item['item_subtotal']=$item_amounts->item_subtotal;
                $inv_item['item_tax_total']=$item_amounts->item_tax_total;
                $inv_item['item_discount']=$item_amounts->item_discount;
                $inv_item['item_total']=$item_amounts->item_total;
                $this->db->insert('ip_invoice_item_amounts',$inv_item);
            }

        $up_po['po_status']='1';
        $up_po['po_date_modified']=date('Y-m-d H:i:s');
        $this->db->where('po_id',$po_id);
        $this->db->update('ip_po',$up_po);
        //INSERT ITEM END
        redirect('invoices/view/'.$invoice_id,'location');
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';

    }
    public function get_invoice_number($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }
    public function get_date_due($invoice_date_created)
    {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . get_setting('invoices_due_after') . 'D'));
        return $invoice_date_due->format('Y-m-d');
    }
    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    public function generate_do_number()
    {
        $thn=date('Y');
        $po=$this->db->query('select * from ip_invoices where YEAR(invoice_date_created)='.$thn.' order by invoice_id desc limit 1')->result();
        $num=0;
        
        if(count($po)==0)
            $num=1;
        else
        {
            foreach($po as $item)
            {
                $n=strtok($item->do_number,'/');
                $num=$n+1;
            }
        }

        if($num<10)
            $num='00'.$num;
        elseif($n>=10 && $n<100)
            $num='0'.$num;
        else
            $num=$num;

        return $num.'/JDK-DO/'.romawi(date('n')).'/'.date('Y');
        // return $num.'/OSM-DO/'.romawi(date('n')).'/'.date('Y');

    }
    public function generate_kw_number()
    {
        $thn=date('Y');
        $po=$this->db->query('select * from ip_invoices where YEAR(invoice_date_created)='.$thn.' order by invoice_id desc limit 1')->result();
        $num=0;
        
        if(count($po)==0)
            $num=1;
        else
        {
            foreach($po as $item)
            {
                $n=strtok($item->no_kwitansi,'/');
                $num=$n+1;
            }
        }

        if($num<10)
            $num='00'.$num;
        elseif($n>=10 && $n<100)
            $num='0'.$num;
        else
            $num=$num;

        return $num.'/JDK-KWT/'.romawi(date('n')).'/'.date('Y');
        // return $num.'/OSM-KWT/'.romawi(date('n')).'/'.date('Y');

    }

    public function view($poid)
    {
        $invoice=$this->db->from('ip_invoices')->where('po_id',$poid)->get()->row();
        if(count($invoice)!=0)
            redirect('invoices/view/'.$invoice->invoice_id,'location');
        else
        {
            $po=$this->db->from('ip_po')->where('po_id',$poid)->get()->row();
            redirect('po/status/'.$po->status_po.'-all','location');
        }
    }
}