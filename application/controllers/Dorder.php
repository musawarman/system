<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dorder extends Admin_Controller
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

    public function index($jns)
    {
        redirect('dorder/status/'.$jns);
    }

    public function status($jns='in-all')
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        list($st,$jns)=explode('-',$jns);

        $data_po=array();
        $data['jns']=$jns;
        
        $invoices=$this->db->from('ip_invoices')->where('status_invoices',$st)->get()->result();
        $invoice=$dinvoice=array();
        foreach($invoices as $k)
        {
            $dinvoice[$k->invoice_id]=$k;
            $invoice[]=$k->invoice_id;
        }

        $st_po=($st=='out'? 'in' : 'out');

        $data_po=$this->db->from('ip_po')->where('po_status!=',-1)->where('status_po',$st_po)->order_by('po_date','desc')->get()->result();
        $porder=$dporder=array();
        foreach($data_po as $k)
        {
            $dporder[$k->po_id]=$k;
            $porder[]=$k->po_id;
        }

        // $dorder=$this->db->from('ip_do')->where_in('invoice_id',$invoice)->get()->result();
        if(count($invoice)!=0)
            $dorder=$this->db->from('ip_do')->where_in('po_id',$porder)->get()->result();
        else
            $dorder=array();

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
                'jns' => $jns,
                'data_po' => $data_po,
                'st' => $st,
                'quotes' => $quotes,
                'dorder' => $dorder,
                'dporder' => $dporder,
                'porder' => $porder,
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

        $this->layout->buffer('content', 'dorder/index');
        $this->layout->render();
    }
    public function form($jns='out-new')
    {
        $jns=strtok($jns,'-');
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        
        $st_po=($jns=='out'? 'in' : 'out');

        $det=$dorder=array(); 
        $det=$data_po=$this->db->from('ip_po')->where('status_po',$st_po)->where('po_status !=',-1)->get()->result();
        // $det=$data_po=$this->db->from('ip_po')->where('status_po',$jns)->where('po_status !=',-1)->get()->result();
        $porder=$dporder=array();
        foreach($det as $k)
        {
            $dporder[$k->po_id]=$k;
            $porder[]=$k->po_id;
        }
        // echo '<pre>';
        // print_r($det);
        // echo '</pre>';
        $data['jns']=$jns;

        $invoices=$this->db->from('ip_invoices')->where('status_invoices',$jns)->where('quote_id!=',0)->get()->result();
        
        $dorder=$this->db->from('ip_do')->get()->result();

        $dinvoice=array();
        foreach($invoices as $k)
        {
            $dinvoice[$k->invoice_id]=$k;
        }

        $dos=array();
        foreach($dorder as $k)
        {
            // $dos[]=$k->invoice_id;
            $dos[]=$k->po_id;
        }


        $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->get()->result();
        $quotes=array();
        foreach($quote as $item)
        {
            $quotes[$item->quote_id]=$item;
        }
       
        $quotes_=$this->db->from('ip_quotes')->get()->result();
        $quotess=array();
        foreach($quotes_ as $item)
        {
            $quotess[$item->quote_id]=$item;
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
        // print_r($quotess);
        // echo '</pre>';
        $number_do=$this->generate_do_number();
        $this->layout->set(
            array(
                'data_po' => $data_po,
                'det' => $det,
                'paid' => $paid,
                'dos' => $dos,
                'number_do' => $number_do,
                'jns' => $jns,
                'quotes' => $quotes,
                'quotess' => $quotess,
                'dporder' => $dporder,
                'porder' => $porder,
                'suppliers' => $suppliers,
                'vendors' => $vendors,
                'clients' => $clients,
                'invoices' => $invoices,
                'dinvoice' => $dinvoice,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        
        $this->layout->buffer('content', 'dorder/form');
            
        $this->layout->render();
    }
    public function formout($invoice_id=null)
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        
        $jns='out';
        $st_po='in';

        $det=$dorder=array(); 
        
        $det=$data_po=$this->db->from('ip_po')->where('po_id',$invoice_id)->get()->result();
        $porder=$dporder=array();
        foreach($det as $k)
        {
            $dporder[$k->po_id]=$k;
            $porder[]=$k->po_id;
        }
        $data['jns']=$jns;
        
        $invoices=$this->db->from('ip_invoices')->where('invoice_id',$invoice_id)->get()->result();
        $dorder=$this->db->from('ip_do')->where_in('invoice_id',$invoice_id)->get()->result();
        $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->get()->result();
        $quotes=array();
        foreach($quote as $item)
        {
            $quotes[$item->quote_id]=$item;
        }

        $quotes_=$this->db->from('ip_quotes')->get()->result();
        $quotess=array();
        foreach($quotes_ as $item)
        {
            $quotess[$item->quote_id]=$item;
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
        $number_do=$this->generate_do_number();
        $this->layout->set(
            array(
                'data_po' => $data_po,
                'dorder' => $dorder,
                'det' => $det,
                'paid' => $paid,
                'number_do' => $number_do,
                'invoice_id' => $invoice_id,
                'jns' => $jns,
                'quotes' => $quotes,
                'quotess' => $quotess,
                'suppliers' => $suppliers,
                'vendors' => $vendors,
                'clients' => $clients,
                'invoices' => $invoices,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        
        $this->layout->buffer('content', 'dorder/form-out');
            
        $this->layout->render();
    }
    public function formin($invoice_id=null)
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        
        $jns='out';
        $st_po='in';

        $det=array(); 
        // $det=$data_po=$this->db->from('ip_po')->where('status_po',$st_po)->where('po_status !=',-1)->get()->result();
        $det=$data_po=$this->db->from('ip_po')->where('po_id',$invoice_id)->get()->result();
        $porder=$dporder=array();
        foreach($det as $k)
        {
            $dporder[$k->po_id]=$k;
            $porder[]=$k->po_id;
        }
        $data['jns']=$jns;
        
        $invoices=$this->db->from('ip_invoices')->where('invoice_id',$invoice_id)->get()->result();
        $dorder=$this->db->from('ip_do')->where_in('invoice_id',$invoice_id)->get()->result();
        $quote=$this->db->from('ip_quotes')->where('quote_status_id',4)->get()->result();
        $quotes=array();
        foreach($quote as $item)
        {
            $quotes[$item->quote_id]=$item;
        }
         $quotes_=$this->db->from('ip_quotes')->get()->result();
        $quotess=array();
        foreach($quotes_ as $item)
        {
            $quotess[$item->quote_id]=$item;
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
        $number_do=$this->generate_do_number();
        $this->layout->set(
            array(
                'dorder' => $dorder,
                'data_po' => $data_po,
                'det' => $det,
                'paid' => $paid,
                'number_do' => $number_do,
                'invoice_id' => $invoice_id,
                'jns' => $jns,
                'quotes' => $quotes,
                'quotess' => $quotess,
                'suppliers' => $suppliers,
                'vendors' => $vendors,
                'clients' => $clients,
                'invoices' => $invoices,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        
        $this->layout->buffer('content', 'dorder/form-in');
            
        $this->layout->render();
    }

    public function proses($invoice_id=null)
    {
        $data=$this->input->post();
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        if($invoice_id!=null)
        {
            // $cekinv=$this->db->from('ip_do')->where('invoice_id',$invoice_id)->get()->row();
            $cekinv=$this->db->from('ip_do')->where('po_id',$invoice_id)->get()->row();
            if($cekinv)
            {
                $this->db->query('delete from ip_do where do_id="'.$cekinv->do_id.'"');
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
                    $upd['do_file']='';
                }
                else
                {
                    $upl=$this->upload->data();
                    $nama_file=$upl['file_name'];
                    $upload = array('upload_data' => $upl);
                    $upd['do_file']=$nama_file;
                }
            }
            else
            {
                $upd['do_file']='';
            }

            if($invoice_id=='in' || $invoice_id=='out')
            {
                // $ins['invoice_id']=$invoice_id=$data['invoice_id'];
                $ins['po_id']=$po_id=$invoice_id=$data['invoice_id'];
            }
            else
            {
                // $ins['invoice_id']=$invoice_id;
                $ins['po_id']=$po_id=$invoice_id;
            }
            $ins['do_number']=$data['do_number'];
            $ins['do_buyer']=$data['do_buyer'];
            $ins['do_fob']=$data['do_fob'];
            $ins['do_delivery_term']=$data['do_delivery_term'];
            $ins['do_ship_date']=date('Y-m-d',strtotime($data['invoice_date_created']));;
            $ins['do_sales_person']=$data['do_sales_person'];
            $ins['do_shipped_via']=$data['do_shipped_via'];
            $ins['do_status']=$data['status_invoices'];
            $ins['do_created_date']=date('Y-m-d H:i:s');

            $this->db->insert('ip_do',$ins);
            // $getpo=$this->db->from('ip_po')->where('po_id',$po_id)->get()->row();
            // $st
            // $upd['do_number']=$data['do_number'];
            // $this->db->where('invoice_id',$invoice_id);
            // $this->db->update('ip_invoices',$upd);
            // $st=($data['status_invoices']=='out'?'in':'out');
            $st=($data['status_invoices']);
            redirect('dorder/status/'.$st.'-all','location');
        }
    }

    public function generate_pdf($invoice_id)
    {
        // $invoices=$this->db->from('ip_invoices')->where('invoice_id',$invoice_id)->get()->row();
        // $po_s=$this->db->from('ip_po')->where('po_id',$invoices->po_id)->get()->row();
        // $quotes=$this->db->from('ip_quotes')->where('quote_id',$invoices->quote_id)->get()->row();
        // echo '<pre>';
        // print_r($invoices);
        // print_r($po_s);
        // print_r($quotes);
        // echo '</pre>';
        $this->load->helper('pdf');
        generate_do_pdf($invoice_id, true, 'pdf_template');
    }
     public function printcetak($invoice_id, $stream = true, $quote_template = null)
    {
        // $d=$this->db->from('ip_invoices')->where('invoice_id',$invoice_id)->get()->result();
        $dd=$this->db->from('ip_do',$invoice_id)->get()->row();
        $d=$this->db->from('ip_po',$dd->po_id)->get()->result();
        if($d[0]->status_po=='in')
        {
            // $this->load->helper('download');
            if($dd->do_file!='')
            {
                if(strpos($dd->do_file,'pdf')!==false)
                {
                    $data   = file_get_contents('./uploads/import/'.$dd->do_file);
                    $name   = $dd->do_file;
                    $this->output
                        ->set_content_type('application/pdf')
                        ->set_output(file_get_contents('./uploads/import/'.$dd->do_file));
                }
                else
                {
                    echo '<img src="'.base_url().'uploads/import/'.$dd->do_file.'">';
                }
            }
            else
            {
                $this->load->helper('pdf');
                generate_do_pdf($invoice_id, true, 'pdf_template');
            }
        }
        else
        {
            $this->load->helper('pdf');
            generate_do_pdf($invoice_id, true, 'pdf_template');
        }
    }
    public function printkwitansi($invoice_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');
        generate_kwitansi_pdf($invoice_id, true, 'kwitansi_template');
        
    }
    public function generate_do_number()
    {
        $thn=date('Y');
        $po=$this->db->query('select * from ip_do where YEAR(do_created_date)='.$thn.' and do_status="out" order by do_id desc limit 1')->result();
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

    public function delete($id)
    {
        $this->db->query('delete from ip_do where do_id="'.$id.'"');
        $this->session->set_flashdata('alert_success','Delete Data DO Berhasil');
        // redirect('do/index/'.($st),'location');
        redirect($_SERVER['HTTP_REFERER']);
    }
}