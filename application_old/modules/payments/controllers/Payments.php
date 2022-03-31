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
 * Class Payments
 */
class Payments extends Admin_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_payments');
    }

    /**
     * @param int $page
     */
    public function index($jns=null,$page = 0)
    {
        $this->mdl_payments->paginate(site_url('payments/index'), $page);

        $payments = $this->mdl_payments->result();
        
        $this->layout->set(
            array(
                'payments' => $payments,
                'jns' => $jns,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_payments'),
                'filter_method' => 'filter_payments'
            )
        );

        $this->layout->buffer('content', 'payments/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        $this->load->model('custom_fields/mdl_payment_custom');
        if ($this->input->post('btn_cancel')) {
            redirect('payments');
        }

        if ($this->mdl_payments->run_validation()) {
            $id = $this->mdl_payments->save($id);

            

            $this->mdl_payment_custom->save_custom($id, $this->input->post('custom'));

            redirect('payments');
        }

        if (!$this->input->post('btn_submit')) {
            $prep_form = $this->mdl_payments->prep_form($id);

            if ($id and !$prep_form) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_payment_custom');
            $this->load->model('custom_values/mdl_custom_values');

            $payment_custom = $this->mdl_payment_custom->where('payment_id', $id)->get();

            if ($payment_custom->num_rows()) {
                $payment_custom = $payment_custom->row();

                unset($payment_custom->payment_id, $payment_custom->payment_custom_id);

                foreach ($payment_custom as $key => $val) {
                    $this->mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } else {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->helper('custom_values');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');

        $open_invoices = $this->mdl_invoices
            ->where('ip_invoice_amounts.invoice_balance >', 0)
            ->or_where('ip_invoice_amounts.invoice_balance <', 0)
            ->get()->result();

        $custom_fields = $this->mdl_custom_fields->by_table('ip_payment_custom')->get()->result();
        $custom_values = [];

        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_payment_custom->get_by_payid($id);

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->payment_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_payments->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->payment_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $amounts = array();
        $invoice_payment_methods = array();
        foreach ($open_invoices as $open_invoice) {
            $amounts['invoice' . $open_invoice->invoice_id] = format_amount($open_invoice->invoice_balance);
            $invoice_payment_methods['invoice' . $open_invoice->invoice_id] = $open_invoice->payment_method;
        }

        $this->layout->set(
            array(
                'payment_id' => $id,
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'open_invoices' => $open_invoices,
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'amounts' => json_encode($amounts),
                'invoice_payment_methods' => json_encode($invoice_payment_methods)
            )
        );

        if ($id) {
            $this->layout->set('payment', $this->mdl_payments->where('ip_payments.payment_id', $id)->get()->row());
        }

        $this->layout->buffer('content', 'payments/form');
        $this->layout->render();
    }

    

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_payments->delete($id);
        redirect('payments');
    }

    public function printkwitansi($payment_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');
        generate_kwitansi_pdf($payment_id, true, 'kwitansi_template');
        
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
}
