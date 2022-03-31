<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Procal extends Admin_Controller
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

        $this->layout->buffer('content', 'procal/index');
        $this->layout->render();
    }

    public function data($bln,$thn)
    {
        $data['bulan']=$bln;
        $data['tahun']=$thn;

        $data_po=$this->db->from('ip_po')->where('po_status!=',-1)->where('MONTH(po_date)',$bln)->where('YEAR(po_date)',$thn)->order_by('po_date','asc')->get()->result();
        $data['data_po']=$data_po;

        $items=$this->db->from('ip_quote_items')->get()->result();
        $taxs=$this->db->from('ip_quote_tax_rates')->get()->result();
        $amounts=$this->db->from('ip_quote_amounts')->get()->result();
        $item=$tax=$amount=array();
        foreach($amounts as $v)
        {
            $amount[$v->quote_id]=$v;
            
        }
        foreach($taxs as $v)
        {
            $tax[$v->quote_id]=$v;
            
        }
        foreach($items as $itm)
        {
            $item[$itm->quote_id][]=$itm;
            
        }
        $data['items']=$item;
        $data['tax']=$tax;
        $data['amount']=$amount;

        $ip_cogs = $this->db->from('ip_cogs')->get()->result();
        $ip_pajak = $this->db->from('ip_pajak')->get()->result();
        $ip_biaya_lain = $this->db->from('ip_biaya_lain')->get()->result();
        $cogs=$pajak=$biaya_lain=array();
        foreach($ip_cogs as $v)
        {
            // $cogs[$v->po_id][$v->item_id]=$v;
            $cogs[$v->po_id][createSlug($v->item_name)]=$v;
        }
        foreach($ip_pajak as $v)
        {
            $pajak[$v->po_id][]=$v;
        }
        foreach($ip_biaya_lain as $v)
        {
            $biaya_lain[$v->po_id][]=$v;
        }
        $data['cogs']=$cogs;
        $data['biaya_pajak']=$pajak;
        $data['biaya_lain']=$biaya_lain;
        $this->load->view('procal/data',$data);
    }
    public function simpancogs()
    {
        $data=$this->input->post();

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';

        $po_id=$data['id_po'];
        $this->db->where('po_id',$po_id);
        $this->db->delete('ip_cogs');

        foreach($data['nilaicogs'] as $iditem=>$k)
        {
            $items=$this->db->from('ip_quote_items')->where('item_product_id',$iditem)->get()->row();
            if(count($items)!=0)
            {
                $ins['item_id']=$iditem;
                $ins['po_id']=$po_id;
                $ins['item_name']=$items->item_name;
                $ins['item_quantity']=$items->item_quantity;
                $ins['item_price']=$items->item_price;
                $ins['item_nominal']=$k;
                $ins['pajak']=$data['pajak'];
                $ins['item_product_unit']=$items->item_product_unit;
            }
            else
            {
                $ins['item_id']=$iditem;
                $ins['po_id']=$po_id;
                $ins['item_name']=$data['name'][$iditem];
                $ins['item_quantity']=$data['quantity'][$iditem];
                $ins['item_price']=$data['price'][$iditem];
                $ins['item_nominal']=$k;
                $ins['pajak']=$data['pajak'];
                $ins['item_product_unit']=$data['product_unit'][$iditem];
            }
            $this->db->insert('ip_cogs',$ins);
        }

        $this->session->set_flashdata('alert_success','Simpan Data COGS Berhasil');
        redirect('procal','location');
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
    }

    public function datacogs($poid,$quote_id)
    {
        echo '<input type="hidden" name="id_po" value="'.$poid.'">';
        echo '<input type="hidden" name="quote_id" value="'.$quote_id.'">';
        $items=$this->db->from('ip_quote_items')->where('quote_id',$quote_id)->get()->result();
        foreach($items as $item)
        {
            $title=createSlug($item->item_name);
            echo '<div class="form-group">
                <label for="quote_password">'.$item->item_name.'</label>
                <input type="text" placeholder="Nilai COGS" name="nilaicogs['.$title.']" id="" class="form-control" value="" autocomplete="off">
                <input type="hidden" name="quantity['.$title.']" value="'.$item->item_quantity.'">
                <input type="hidden" name="price['.$title.']" value="'.$item->item_price.'">
                <input type="hidden" name="name['.$title.']" value="'.$item->item_name.'">
                <input type="hidden" name="product_unit['.$title.']" value="'.$item->item_product_unit.'">
            </div>';
        }
        echo '<div class="form-group">
                <label for="quote_password">Besaran Pajak</label>
                <input type="text" placeholder="Besaran Pajak (%)" name="pajak" id="" class="form-control"
                       value="" autocomplete="off">
            </div>';
        
    }
    public function datapajak($poid,$quote_id)
    {
        echo '<input type="hidden" name="id_po" value="'.$poid.'">';
        echo '<input type="hidden" name="quote_id" value="'.$quote_id.'">';
        
        $items=itempajak();
        foreach($items as  $key=>$item)
        {
            echo '<div class="form-group" style="width:100%;float:left">
                <label for="quote_password" style="width:100%;float:left">'.$item.'</label>
                <input type="text" placeholder="Persen(%)" name="persenpajak['.$key.']" id="" class="form-control"
                       value="0" autocomplete="off" style="width:20%;float:left;">
                <input type="text" placeholder="'.$item.'" name="nilaipajak['.$key.']" id="" class="form-control"
                       value="0" autocomplete="off" style="width:49%;float:left;">
            </div>';
        }
       
        
    }
    public function simpanpajak()
    {
        $data=$this->input->post();
        $po_id=$data['id_po'];
        $this->db->where('po_id',$po_id);
        $this->db->delete('ip_pajak');
        $itempajak=itempajak();
        foreach($data['nilaipajak'] as $key=>$k)
        {
            $ins['po_id']=$po_id;
            $ins['pajak_name']=$itempajak[$key];
            $ins['pajak_persen']=$data['persenpajak'][$key];
            $ins['pajak_nominal']=$k;
            
            $this->db->insert('ip_pajak',$ins);
        }

        $this->session->set_flashdata('alert_success','Simpan Data Biaya Pajak Berhasil');
        redirect('procal','location');
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
    }
    public function datalain($poid,$quote_id)
    {
        echo '<input type="hidden" name="id_po" value="'.$poid.'">';
        echo '<input type="hidden" name="quote_id" value="'.$quote_id.'">';
        
        $items=itembiayalain();
        foreach($items as  $key=>$item)
        {
            if($item=='Administrasi Project')
            {
                echo '<div class="form-group" style="width:100%;float:left">
                    <label for="quote_password" style="width:100%;float:left"><b>'.$item.'</b></label>
                </div>';
            }
            else
            {
                echo '<div class="form-group" style="width:100%;float:left">
                    <label for="quote_password" style="width:100%;float:left;"><b>'.$item.'</b></label>
                    <input type="text" placeholder="Persen(%)" name="persenbiayalain['.$key.']" id="" class="form-control"
                       value="0" autocomplete="off" style="width:15%;float:left;">
                    <input type="text" placeholder="'.$item.'" name="nilaibiayalain['.$key.']" id="" class="form-control"
                        value="0" autocomplete="off" style="width:49%;float:left;text-align:right">
                </div>';
            }
        }
       
        
    }
    public function simpanlain()
    {
        $data=$this->input->post();
        $po_id=$data['id_po'];
        $this->db->where('po_id',$po_id);
        $this->db->delete('ip_biaya_lain');
        $itempajak=itembiayalain();
        foreach($data['nilaibiayalain'] as $key=>$k)
        {
            $ins['po_id']=$po_id;
            $ins['biaya_name']=$itempajak[$key];
            $ins['biaya_persen']=$data['persenbiayalain'][$key];
            $ins['biaya_nominal']=$k;
            
            $this->db->insert('ip_biaya_lain',$ins);
        }

        $this->session->set_flashdata('alert_success','Simpan Data Biaya Lain Berhasil');
        redirect('procal','location');
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
    }

    public function excel($poid,$quote_id)
    {
        $data_po=$this->db->from('ip_po')->where('po_id',$poid)->get()->row();
        $data['data_po']=$data_po;

        $items=$this->db->from('ip_quote_items')->where('quote_id',$quote_id)->get()->result();
        $taxs=$this->db->from('ip_quote_tax_rates')->where('quote_id',$quote_id)->get()->result();
        $amounts=$this->db->from('ip_quote_amounts')->where('quote_id',$quote_id)->get()->result();
        $item=$tax=$amount=array();
        foreach($amounts as $v)
        {
            $amount[$v->quote_id]=$v;
            
        }
        foreach($taxs as $v)
        {
            $tax[$v->quote_id]=$v;
            
        }
        $namapr='Pengadaan ';
        foreach($items as $itm)
        {
            $item[$itm->quote_id][]=$itm;
            $namapr.=$itm->item_name.',';
        }
        $data['items']=$item;
        $data['tax']=$tax;
        $data['amount']=$amount;

        $ip_cogs = $this->db->from('ip_cogs')->where('po_id',$poid)->get()->result();
        $ip_pajak = $this->db->from('ip_pajak')->where('po_id',$poid)->get()->result();
        $ip_biaya_lain = $this->db->from('ip_biaya_lain')->where('po_id',$poid)->get()->result();
        $cogs=$pajak=$biaya_lain=array();
        foreach($ip_cogs as $v)
        {
            // $cogs[$v->po_id][$v->item_id]=$v;
            $cogs[$v->po_id][createSlug($v->item_name)]=$v;
        }
        foreach($ip_pajak as $v)
        {
            $pajak[$v->po_id][]=$v;
        }
        foreach($ip_biaya_lain as $v)
        {
            $biaya_lain[$v->po_id][]=$v;
        }
        $data['cogs']=$cogs;
        $data['namapr']=$namapr;
        $data['biaya_pajak']=$pajak;
        $data['biaya_lain']=$biaya_lain;

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
        $quote=$this->db->from('ip_quotes')->where('quote_id',$quote_id)->get()->row();
        if($quote->vendor_id!=0)
            $vnd=$vendors[$quote->vendor_id]->vendor_name;
        else
            $vnd=$clients[$quote->client_id]->client_name;

        $data['vnd']=$vnd;
        $this->load->view('procal/excel',$data);

    }
}