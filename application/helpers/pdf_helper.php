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
 * Generate the PDF for an invoice
 *
 * @param $invoice_id
 * @param bool $stream
 * @param null $invoice_template
 * @param null $is_guest
 * @return string
 */
function generate_invoice_pdf($invoice_id, $stream = true, $invoice_template = null, $is_guest = null)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_items');
    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_invoice_tax_rates');
    $CI->load->model('custom_fields/mdl_custom_fields');
    $CI->load->model('payment_methods/mdl_payment_methods');

    $CI->load->helper('country');
    $CI->load->helper('client');

    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
    $invoice = $CI->mdl_invoices->get_payments($invoice);

    // Override language with system language
    set_language($invoice->client_language);

    if (!$invoice_template) {
        $CI->load->helper('template');
        $invoice_template = select_pdf_invoice_template($invoice);
    }

    $payment_method = $CI->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
    if ($invoice->payment_method == 0) {
        $payment_method = false;
    }


    $po=$CI->db->from('ip_po')->where('po_id',$invoice->po_id)->get()->row();
    $do=$CI->db->from('ip_do')->where('invoice_id',$invoice_id)->get()->row();
    // Determine if discounts should be displayed
    $items = $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result();

    // Discount settings
    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
        }
    }

    // Get all custom fields
    $custom_fields = array(
        'invoice' => $CI->mdl_custom_fields->get_values_for_fields('mdl_invoice_custom', $invoice->invoice_id),
        'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $invoice->client_id),
        'user' => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $invoice->user_id),
    );

    if ($invoice->quote_id) {
        $custom_fields['quote'] = $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $invoice->quote_id);
    }

    // PDF associated files
    $include_zugferd = $CI->mdl_settings->setting('include_zugferd');

    if ($include_zugferd) {
        $CI->load->helper('zugferd');

        $associatedFiles = array(
            array(
                'name' => 'ZUGFeRD-invoice.xml',
                'description' => 'ZUGFeRD Invoice',
                'AFRelationship' => 'Alternative',
                'mime' => 'text/xml',
                'path' => generate_invoice_zugferd_xml_temp_file($invoice, $items)
            )
        );
    } else {
        $associatedFiles = null;
    }

    $data = array(
        'invoice' => $invoice,
        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
        'items' => $items,
        'po' => $po,
        'do' => $do,
        'payment_method' => $payment_method,
        'output_type' => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields' => $custom_fields,
    );

    $html = $CI->load->view('invoice_templates/pdf/' . $invoice_template, $data, true);
    // $html = $CI->load->view('invoice_templates/pdf/InvoicePlaneOSM', $data, true);
    // echo $html;
    // echo $invoice_template;
    $CI->load->helper('mpdf');
    return pdf_create($html, trans('invoice') . '_' . str_replace(array('\\', '/'), '_', $invoice->invoice_number),
        $stream, $invoice->invoice_password, true, $is_guest, $include_zugferd, $associatedFiles);
}

function generate_invoice_sumex($invoice_id, $stream = true, $client = false)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_items');
    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
    $CI->load->library('Sumex', array(
        'invoice' => $invoice,
        'items' => $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result()
    ));

    // Append a copy at the end and change the title:
    // WARNING: The title depends on what invoice type is (TP, TG)
    // and is language-dependant. Fix accordingly if you really need this hack
    $temp = tempnam("/tmp", "invsumex_");
    $tempCopy = tempnam("/tmp", "invsumex_");
    $pdf = new FPDI();
    $sumexPDF = $CI->sumex->pdf();

    $sha1sum = sha1($sumexPDF);
    $shortsum = substr($sha1sum, 0, 8);
    $filename = trans('invoice') . '_' . $invoice->invoice_number . '_' . $shortsum;

    if (!$client) {
        file_put_contents($temp, $sumexPDF);

        // Hackish
        $sumexPDF = str_replace(
            "Giustificativo per la richiesta di rimborso",
            "Copia: Giustificativo per la richiesta di rimborso",
            $sumexPDF
        );

        file_put_contents($tempCopy, $sumexPDF);

        $pageCount = $pdf->setSourceFile($temp);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            if ($size['w'] > $size['h']) {
                $pageFormat = 'L';  //  landscape
            } else {
                $pageFormat = 'P';  //  portrait
            }

            $pdf->addPage($pageFormat, array($size['w'], $size['h']));
            $pdf->useTemplate($templateId);
        }

        $pageCount = $pdf->setSourceFile($tempCopy);

        for ($pageNo = 2; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            if ($size['w'] > $size['h']) {
                $pageFormat = 'L';  //  landscape
            } else {
                $pageFormat = 'P';  //  portrait
            }

            $pdf->addPage($pageFormat, array($size['w'], $size['h']));
            $pdf->useTemplate($templateId);
        }

        unlink($temp);
        unlink($tempCopy);

        if ($stream) {
            header("Content-Type", "application/pdf");
            $pdf->Output($filename . '.pdf', 'I');
            return;
        }

        $filePath = UPLOADS_FOLDER . 'temp/' . $filename . '.pdf';
        $pdf->Output($filePath, 'F');
        return $filePath;
    } else {
        if ($stream) {
            return $sumexPDF;
        }

        $filePath = UPLOADS_FOLDER . 'temp/' . $filename . '.pdf';
        file_put_contents($filePath, $sumexPDF);
        return $filePath;
    }
}

/**
 * Generate the PDF for a quote
 *
 * @param $quote_id
 * @param bool $stream
 * @param null $quote_template
 * @return string
 */
function generate_quote_pdf($quote_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->model('custom_fields/mdl_custom_fields');
    $CI->load->helper('country');
    $CI->load->helper('client');

    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    // Override language with system language
    set_language($quote->client_language);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    // Determine if discounts should be displayed
    $items = $CI->mdl_quote_items->where('quote_id', $quote_id)->get()->result();

    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
        }
    }

    // Get all custom fields
    $custom_fields = array(
        'quote' => $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $quote->quote_id),
        'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $quote->client_id),
        'user' => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $quote->user_id),
    );

    $data = array(
        'quote' => $quote,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items' => $items,
        'output_type' => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields' => $custom_fields,
    );

    // $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, true);
    $html = $CI->load->view('quote_templates/pdf/InvoicePlane', $data, true);

    $CI->load->helper('mpdf');

    return pdf_create($html, trans('quote') . '_' . str_replace(array('\\', '/'), '_', $quote->quote_number), $stream, $quote->quote_password);
}

function generate_po_pdf($po_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->model('custom_fields/mdl_custom_fields');
    $CI->load->helper('country');
    $CI->load->helper('client');

    $det=$CI->db->from('ip_po')->where('po_id',$po_id)->get()->result();

    $quotess=$CI->db->from('ip_quotes')->where('quote_id',$det[0]->quotes_id)->get()->result();
    
    $vendor=$CI->db->from('ip_vendors')->get()->result();
    $vendors=array();
    foreach($vendor as $item)
    {
        $vendors[$item->vendor_id]=$item;
    }
    $client=$CI->db->from('ip_clients')->get()->result();
    $clients=array();
    foreach($client as $item)
    {
        $clients[$item->client_id]=$item;
    }
    
    $supplier=$CI->db->from('ip_supplier')->get()->result();
    $suppliers=array();
    foreach($supplier as $item)
    {
        $suppliers[$item->supplier_id]=$item;
    }
    
    
    
    
    
    // print_r($det);
    $quote = $CI->mdl_quotes->get_by_id($det[0]->quotes_id);

    // Override language with system language
    set_language($quote->client_language);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    // Determine if discounts should be displayed
    $items = $CI->mdl_quote_items->where('quote_id', $det[0]->quotes_id)->get()->result();

    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
        }
    }

    // Get all custom fields
    $custom_fields = array(
        'quote' => $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $quote->quote_id),
        'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $quote->client_id),
        'user' => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $quote->user_id),
    );

    $data = array(
        'quote' => $quote,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $det[0]->quotes_id)->get()->result(),
        'items' => $items,
        'output_type' => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields' => $custom_fields,
        'det'=>$det[0],
        'quotes'=>$quotess[0],
        'vendors'=>$vendors,
        'clients'=>$clients,
        'suppliers'=>$suppliers,
    );

    // $html = $CI->load->view('po/' . $quote_template, $data, true);
    $html = $CI->load->view('po/po_pdf_template', $data, true);

    $CI->load->helper('mpdf');

    return pdf_create($html, 'PO_' . str_replace(array('\\', '/'), '_', $det[0]->po_number), $stream, '');
}
function generate_do_pdf($invoice_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->model('custom_fields/mdl_custom_fields');
    $CI->load->helper('country');
    $CI->load->helper('client');

    // $invoices=$CI->db->from('ip_invoices')->where('invoice_id',$invoice_id)->get()->row();
    $dorder=$CI->db->from('ip_do')->where('do_id',$invoice_id)->get()->row();
    $det=$CI->db->from('ip_po')->where('po_id',$dorder->po_id)->get()->result();
    $quotess=$CI->db->from('ip_quotes')->where('quote_id',$det[0]->quotes_id)->get()->result();
    
    // $det=$CI->db->from('ip_po')->where('po_id',$po_id)->get()->result();

    // $quotess=$CI->db->from('ip_quotes')->where('quote_id',$det[0]->quotes_id)->get()->result();
    
    $vendor=$CI->db->from('ip_vendors')->get()->result();
    $vendors=array();
    foreach($vendor as $item)
    {
        $vendors[$item->vendor_id]=$item;
    }
    $client=$CI->db->from('ip_clients')->get()->result();
    $clients=array();
    foreach($client as $item)
    {
        $clients[$item->client_id]=$item;
    }
    
    $supplier=$CI->db->from('ip_supplier')->get()->result();
    $suppliers=array();
    foreach($supplier as $item)
    {
        $suppliers[$item->supplier_id]=$item;
    }
    
    $quote = $CI->mdl_quotes->get_by_id($det[0]->quotes_id);

    // Override language with system language
    set_language($quote->client_language);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    // Determine if discounts should be displayed
    $items = $CI->mdl_quote_items->where('quote_id', $det[0]->quotes_id)->get()->result();

    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
        }
    }

    // Get all custom fields
    $custom_fields = array(
        'quote' => $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $quote->quote_id),
        'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $quote->client_id),
        'user' => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $quote->user_id),
    );
    
    $invoices = $CI->db->from('ip_invoices')->where('quote_id',$quote->quote_id)->get()->row();

    $data = array(
        'quote' => $quote,
        'dorder' => $dorder,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $det[0]->quotes_id)->get()->result(),
        'items' => $items,
        'output_type' => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields' => $custom_fields,
        'det'=>$det[0],
        'quotes'=>$quotess[0],
        'vendors'=>$vendors,
        'invoices'=>$invoices,
        'clients'=>$clients,
        'suppliers'=>$suppliers,
    );
    // echo $quote_template;
    // $html = $CI->load->view('dorder/pdf_templateOSM', $data, true);
    $html = $CI->load->view('dorder/' . $quote_template, $data, true);
    // $CI->load->view('dorder/' . $quote_template, $data);

    $CI->load->helper('mpdf');

    return pdf_create($html, 'DO_' . str_replace(array('\\', '/'), '_', $dorder->do_number), $stream, '');
}

function generate_kwitansi_pdf($invoice_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->model('custom_fields/mdl_custom_fields');
    $CI->load->helper('country');
    $CI->load->helper('client');
    // $payment=$CI->db->from('ip_payments')->where('payment_id',$payment_id)->get()->row();
    // echo $payment->invoice_id;
    // $invoice_id=$payment->invoice_id;
    $invoices=$CI->db->from('ip_invoices')->where('invoice_id',$invoice_id)->get()->row();
    $invoices_amount=$CI->db->from('ip_invoice_amounts')->where('invoice_id',$invoice_id)->get()->row();
    $det=$CI->db->from('ip_po')->where('po_id',$invoices->po_id)->get()->result();
    $quotess=$CI->db->from('ip_quotes')->where('quote_id',$invoices->quote_id)->get()->result();
    
    // $det=$CI->db->from('ip_po')->where('po_id',$po_id)->get()->result();

    // $quotess=$CI->db->from('ip_quotes')->where('quote_id',$det[0]->quotes_id)->get()->result();
    
    $vendor=$CI->db->from('ip_vendors')->get()->result();
    $vendors=array();
    foreach($vendor as $item)
    {
        $vendors[$item->vendor_id]=$item;
    }
    $client=$CI->db->from('ip_clients')->get()->result();
    $clients=array();
    foreach($client as $item)
    {
        $clients[$item->client_id]=$item;
    }
    
    $supplier=$CI->db->from('ip_supplier')->get()->result();
    $suppliers=array();
    foreach($supplier as $item)
    {
        $suppliers[$item->supplier_id]=$item;
    }
    
    $quote = $CI->mdl_quotes->get_by_id($det[0]->quotes_id);

    // Override language with system language
    set_language($quote->client_language);

    // if (!$quote_template) {
    //     $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    // }

    // Determine if discounts should be displayed
    $items = $CI->mdl_quote_items->where('quote_id', $det[0]->quotes_id)->get()->result();

    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
        }
    }

    // Get all custom fields
    $custom_fields = array(
        'quote' => $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $quote->quote_id),
        'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $quote->client_id),
        'user' => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $quote->user_id),
    );
    $dorder=$CI->db->from('ip_do')->where('invoice_id',$invoice_id)->get()->row();
    $data = array(
        'quote' => $quote,
        'dorder' => $dorder,
        'invoices_amount' => $invoices_amount,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $det[0]->quotes_id)->get()->result(),
        'items' => $items,
        'output_type' => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields' => $custom_fields,
        'det'=>$det[0],
        'quotes'=>$quotess[0],
        'vendors'=>$vendors,
        'invoices'=>$invoices,
        'clients'=>$clients,
        'suppliers'=>$suppliers,
    );
    // echo $quote_template;
    // $html = $CI->load->view('payments/' . $quote_template, $data, true);
    $html=$CI->load->view('dorder/' . $quote_template, $data, true);

    $CI->load->helper('mpdf');

    return pdf_create($html, 'Kwitansi_' . str_replace(array('\\', '/'), '_', $invoices->invoice_number), $stream, '');
}
