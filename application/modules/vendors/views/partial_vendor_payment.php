<?php
$pay=$this->db->select("SQL_CALC_FOUND_ROWS ip_payment_methods.*,ip_invoice_amounts.*,ip_vendors.vendor_name,ip_vendors.vendor_surname,ip_vendors.vendor_id,ip_invoices.invoice_number,ip_invoices.invoice_date_created,ip_payments.*", false)
        ->from('ip_payments')
        ->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id')
        ->join('ip_vendors', 'ip_vendors.vendor_id = ip_invoices.vendor_id')
        ->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id')
        ->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id', 'left')
        ->where('ip_vendors.vendor_id',$vendor->vendor_id)
        ->order_by('ip_payments.payment_date DESC')->get()->result();

echo count($pay);
?>