<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title>Kwitansi</title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
        <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css"> -->
       
</head>
<body style="margin-top:0px; padding-top:0px;">
    <div style="border:2px solid #888;width:100%">
        <table border="0px" cellpadding="5" cellspacing="5" style="width:100%;border-bottom:2px solid #888;">
            <tr>
                <td style="width:35%;text-align:center;vertical-align:middle">
                    <img src="http://system.jedanka.com/uploads/Screen_Shot_2019-04-17_at_12_32_20.png" style="width:200px;">
                </td>
                <td style="width:65%;text-align:center;vertical-align:middle;background:#ddd;">
                    <div style="font-size:17px;"><b>PT JEDANKA GLOBAL SINERGI</b></div>
                    <div style="font-size:12px;margin-top:20px;">
                    <br>
                        Jababeka Innovation Center, Pintu 6<br>
                        Jl. Samsung 2C blok C2T, Jababeka - Bekasi<br>
                        T. +6221-2909-4304 , E. info@jedanka.com
                    </div>
                </td>
            </tr>
        </table>
        <table border="0px" cellpadding="5" cellspacing="5" style="width:100%;">
            <tr>
                <td style="width:40%;text-align:left;vertical-align:middle;">
                    NO. <?=($invoices->no_kwitansi=='' ? '____________________________________' : '<u>'.$invoices->no_kwitansi.'</u>')?>
                </td>
                <td style="width:60%;text-align:center;vertical-align:middle;;">
                    <h3>K W I T A N S I</h3>
                </td>
            </tr>
        </table>
        <table border="0px" cellpadding="5" cellspacing="5" style="width:100%;">
            <tr>
                <td style="width:29%;text-align:left;vertical-align:middle;padding:10px 0;">
                    Telah Diterima Dari 
                </td>
                <td style="width:4px;padding:10px 0;">:</td>
                <td style="width:70%;text-align:left;vertical-align:middle;padding:10px 0;">
                    <?php
                        if($quotes->vendor_id!=0)
                        {
                            echo '<b>'.$vendors[$quotes->vendor_id]->vendor_name.'</b>';
                        }
                        else
                        {
                            echo '<b>'.$clients[$quotes->client_id]->client_name.'</b>';
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width:29%;text-align:left;vertical-align:middle;padding:10px 0;">
                    Uang Sejumlah
                </td>
                <td style="width:4px;padding:10px 0;">:</td>
                <td style="width:70%;text-align:left;vertical-align:middle;padding:10px 0;background:#ccc">
                    # <?=ucwords(terbilang($payment->payment_amount))?> #
                </td>
            </tr>
            <tr>
                <td style="width:29%;text-align:left;vertical-align:middle;padding:10px 0;">
                    Untuk Pembayaran
                </td>
                <td style="width:4px;padding:10px 0;">:</td>
                <td style="width:70%;text-align:left;vertical-align:middle;padding:10px 0;">
                    PO. No. <?=$det->po_number?>, INVOICE NO. <?=$invoices->invoice_number?>
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align:left;vertical-align:middle;padding:10px 0;height:20px;border-bottom:1px solid #888;" colspan="3">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align:left;vertical-align:middle;padding:10px 0;height:20px;border-bottom:1px solid #888;" colspan="3">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align:left;vertical-align:middle;padding:10px 0;height:20px;border-bottom:1px solid #888;" colspan="3">
                    &nbsp;
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
