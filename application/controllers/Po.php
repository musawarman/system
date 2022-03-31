<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Po extends Admin_Controller
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
        redirect('po/status/'.$jns);
    }

    public function status($jns='in-all')
    {
        $this->load->model('mdl_settings');
        $this->load->helper('country');
        list($st,$jns)=explode('-',$jns);

        $data_po=array();
        $data['jns']=$jns;
        if($jns=='all')
            $data_po=$this->db->from('ip_po')->where('po_status!=',-1)->where('status_po',$st)->order_by('po_date','desc')->get()->result();
        else
        {
            if($jns=='draft')
                $data_po=$this->db->from('ip_po')->where('po_status',0)->where('status_po',$st)->order_by('po_date','desc')->get()->result();
            elseif($jns=='process')
                $data_po=$this->db->from('ip_po')->where('po_status',1)->where('status_po',$st)->order_by('po_date','desc')->get()->result();
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

        $data_do=array();
        $do=$this->db->from('ip_do')->get()->result();
        foreach($do as $v)
        {
            $data_do[$v->po_id]=$v;
        }

        $this->layout->set(
            array(
                'jns' => $jns,
                'data_po' => $data_po,
                'st' => $st,
                'quotes' => $quotes,
                'amounts' => $amounts,
                'suppliers' => $suppliers,
                'data_do' => $data_do,
                'vendors' => $vendors,
                'clients' => $clients,
                'countries' => get_country_list(trans('cldr')),
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'selected_country' => get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'po/index');
        $this->layout->render();
    }

    public function form($id='in-new')
    {
        $this->load->model('mdl_settings');
        

        list($jns,$id)=explode('-',$id);
        $po_st=$jns;
        if($jns=='in')
            $q_st='out';
        else
            $q_st='in';

        if($id=='new')
            $id='-1';
        else
            $id=$id;

        $det=array();
        if($id!=-1)
        {
            $det=$this->db->from('ip_po')->where('po_id',$id)->get()->result();
        }

        $data_po=$this->db->from('ip_po')->where('status_po',$jns)->where('po_status!=','-1')->get()->result();
        
        $d_po=array();
        foreach($data_po as $k=>$v)
        {
            $d_po[]=$v->quotes_id;
        }

        $quote=$this->db->from('ip_quotes')->where('quote_status_id =4')->where('status_quotes',$q_st)->get()->result();
        // $quote=$this->db->query('select * from ip_quotes where (quote_status_id =4 or quote_status_id=2) and status_quotes="'.$q_st.'"')->result();
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

        $this->layout->buffer('content', 'po/form');
        $this->layout->render();
        // echo '<pre>';
        // print_r($quotes);
    }

    public function generate_po_number()
    {
        $thn=date('Y');
        $po=$this->db->query('select * from ip_po where YEAR(po_date)='.$thn.' and po_status!=-1 order by po_id desc limit 1')->result();
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

    public function proses($id=-1)
    {
        // echo '<pre>';
        // print_r($this->input->post());
        // echo '</pre>';
        // return 0;
        
        $data=$this->input->post();
        // $data['jns']=
        if($data['po_date']!='')
            list($tgl,$bln,$thn)=explode('-',$data['po_date']);
        else
            list($tgl,$bln,$thn)=explode('-',date('d-m-Y'));

        $config = array(
            'upload_path' => "./uploads/import/",
            'allowed_types' => "gif|jpg|png|jpeg|pdf",
            'overwrite' => TRUE,
            'max_size' => "4096000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
            'max_height' => "2048",
            'max_width' => "2048"
        );
        $this->load->library('upload', $config);

        if($id==-1)
        { 
            if (!empty($_FILES['po_file']['name']))
            {
                if ( ! $this->upload->do_upload('po_file'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    $nama_file='-';
                    //print_r($error);
                }
                else
                {
                    $upl=$this->upload->data();
                    $nama_file=$upl['file_name'];
                    $upload = array('upload_data' => $upl);

                    //print_r($upload);
                }
            }
            else
            {
                $nama_file='-';
            }

            $ins['file']=$nama_file;
            $ins['po_number']=$data['po_number'];
            $ins['po_status']=0;
            $ins['status_po']=$st=$data['q_st'];
            $ins['po_date_created']=date('Y-m-d H:i:s');
            $ins['po_date_modified']=date('Y-m-d H:i:s');
            $ins['po_date']=($thn.'-'.$bln.'-'.$tgl);
            $ins['quotes_id']=$data['quotes_id'];

            if($data['q_st']=='out')
            {
                $ins['supplier_id']=$data['supplier_id'];
                $ins['po_currency']=$data['currency'];
                $ins['po_bank_name']=$data['po_bank_name'];
                $ins['customer_number']=$data['customer_number'];
                $ins['po_account_number']=$data['po_account_number'];
                $ins['po_payment_term']=$data['po_payment_term'];
                $ins['po_freight_term']=$data['po_freight_term'];
                $ins['po_price_basic']=$data['po_price_basic'];
                $ins['po_address_all_queries']=$data['po_address_all_queries'];
            }
            $c=$this->db->insert('ip_po',$ins);

            if($c)
                $this->session->set_flashdata('alert_success','Create New PO Success');
            else
                $this->session->set_flashdata('alert_error','Create New PO Failed');
        }
        else
        {
            if (empty($_FILES['po_file']['name']))
            {
                if ( ! $this->upload->do_upload('po_file'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    $nama_file='-';
                }
                else
                {
                    $upl=$this->upload->data();
                    $nama_file=$upl['file_name'];
                    $upload = array('upload_data' => $upl);
                    $ins['file']=$nama_file;
                }
            }
            else
            {
                $nama_file=$data['po_file_old'];
            }

            $ins['po_number']=$data['po_number'];
            $ins['po_status']=$data['status'];
            $ins['status_po']=$st=$data['q_st'];
            $ins['po_date_created']=date('Y-m-d H:i:s');
            $ins['po_date_modified']=date('Y-m-d H:i:s');
            $ins['po_date']=($thn.'-'.$bln.'-'.$tgl);
            $ins['quotes_id']=$data['quotes_id'];

            if($data['q_st']=='out')
            {
                $ins['supplier_id']=$data['supplier_id'];
                $ins['po_currency']=$data['currency'];
                $ins['po_bank_name']=$data['po_bank_name'];
                $ins['customer_number']=$data['customer_number'];
                $ins['po_account_number']=$data['po_account_number'];
                $ins['po_payment_term']=$data['po_payment_term'];
                $ins['po_freight_term']=$data['po_freight_term'];
                $ins['po_price_basic']=$data['po_price_basic'];
                $ins['po_address_all_queries']=$data['po_address_all_queries'];
            }
            $this->db->where('po_id',$id);
            $c=$this->db->update('ip_po',$ins);

            if($c)
                $this->session->set_flashdata('alert_success','Edit Data PO Success');
            else
                $this->session->set_flashdata('alert_error','Edit Data PO Failed');
        }
        $st=$data['q_st'];
        // if($st=='in')
        //     $st='out';
        // else
        //     $st='in';

        redirect('po/index/'.$st.'-all');
    }

    public function delete($id)
    {
        $d=$this->db->from('ip_po')->where('po_id',$id)->get()->result();
        


        if(count($d)!=0)
        {
            $st=$d[0]->status_po;
            $this->db->where('po_id',$id);
            $c=$this->db->update('ip_po',['po_status'=>'-1']);
            if($c)
            {
                
                $this->session->set_flashdata('alert_success','Delete Data PO Success');
            }
            else
                $this->session->set_flashdata('alert_error','Delete Data PO Fail');
        }
        else
        {
            $st='out';
            $this->session->set_flashdata('alert_error','Delete Data PO Fail');
        }

        redirect('po/index/'.($st).'-all','location');
    }
    public function printdo($idpo)
    {
        $do=$this->db->from('ip_do')->where('po_id',$idpo)->get()->row();
        redirect('dorder/printcetak/'.$do->do_id,'location');
    }
    public function printcetak($idpo, $stream = true, $quote_template = null)
    {
        $d=$this->db->from('ip_po')->where('po_id',$idpo)->get()->result();
        if($d[0]->status_po=='in')
        {
            // $this->load->helper('download');
            if($d[0]->file!='-')
            {
                if(strpos($d[0]->file,'pdf')!==false)
                {
                    $data   = file_get_contents('./uploads/import/'.$d[0]->file);
                    $name   = $d[0]->file;
                    $this->output
                        ->set_content_type('application/pdf')
                        ->set_output(file_get_contents('./uploads/import/'.$d[0]->file));
                }
                else
                {
                    echo '<img src="'.base_url().'uploads/import/'.$d[0]->file.'">';
                }
            }
            else
            {
                $this->load->helper('pdf');
                generate_po_pdf($idpo, $stream, 'po_pdf_template');
            }
            // force_download($name, $data); 
            // $filename = "./uploads/".$det[0]->file;
            // header("Content-type: application/pdf");
            // header("Content-Length: " . filesize($filename));
            // readfile($filename);
        }
        else
        {
            $this->load->helper('pdf');
            generate_po_pdf($idpo, $stream, 'po_pdf_template');
        }
    }
    public function generate_pdf($idpo, $stream = true, $quote_template = null)
    {
        $d=$this->db->from('ip_po')->where('po_id',$idpo)->get()->result();
        if($d[0]->status_po=='in')
        {
            // $this->load->helper('download');
            if(strpos($d[0]->file,'pdf')!==false)
            {
                $data   = file_get_contents('./uploads/import/'.$d[0]->file);
                $name   = $d[0]->file;
                $this->output
                    ->set_content_type('application/pdf')
                    ->set_output(file_get_contents('./uploads/import/'.$d[0]->file));
            }
            else
            {
                echo '<img src="'.base_url().'uploads/import/'.$d[0]->file.'">';
            }
            // force_download($name, $data); 
            // $filename = "./uploads/".$det[0]->file;
            // header("Content-type: application/pdf");
            // header("Content-Length: " . filesize($filename));
            // readfile($filename);
        }
        else
        {
            $this->load->helper('pdf');
            generate_po_pdf($idpo, $stream, 'po_pdf_template');
        }
    }
}