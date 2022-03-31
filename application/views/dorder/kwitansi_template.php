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
                    <!-- <img src="http://system.jedanka.com/uploads/PT_ODS_logo.jpg" style="width:200px;"> -->
                </td>
                <td style="width:65%;text-align:center;vertical-align:middle;background:#ddd;">
                    <div style="font-size:17px;"><b>PT JEDANKA GLOBAL SINERGI</b></div>
                    <!-- <div style="font-size:17px;"><b>PT Oesman Djaya Sejahtera</b></div> -->
                    <div style="font-size:12px;margin-top:20px;">
                    <br>
                        <b>Head Office : </b> BKR Blok J1 No. 6. <br>
                        Purwakarta - Jawa barat. 41181.<br>
                        <b>Branch : </b> Kompleks Griya Caraka Blok G-28. <br>
                        Cisaranten Endah, Arcamanik.<br>
                        Bandung - Jawa Barat 40293.<br>
                        T. +62812-1834-5771 , E. info@jedanka.com<br>
                        W. www.jedanka.com
                        <!-- Jalan Satria Ds. Duren No. 1 Kosambi - Klari <br>
                        Karawang - Jawa Barat 41371<br>
                        E. info@oesmandjayagroup.com -->
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
                <td style="width:29%;text-align:left;vertical-align:middle;padding:5px 0;">
                    Telah Diterima Dari 
                </td>
                <td style="width:4px;padding:5px 0;">:</td>
                <td style="width:70%;text-align:left;vertical-align:middle;padding:5px 0;">
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
                <td style="width:29%;text-align:left;vertical-align:middle;padding:5px 0;">
                    Uang Sejumlah
                </td>
                <td style="width:4px;padding:5px 0;font-size:15;">:</td>
                <td style="width:70%;text-align:left;vertical-align:middle;padding:5px 0;background:#ccc;font-size:15;">
                    # <?=ucwords(terbilang($invoices_amount->invoice_total)).' Rupiah'?> #
                </td>
            </tr>
            <tr>
                <td style="width:29%;text-align:left;vertical-align:middle;padding:5px 0;">
                    Untuk Pembayaran
                </td>
                <td style="width:4px;padding:5px 0;">:</td>
                <td style="width:70%;text-align:left;vertical-align:middle;padding:5px 0;border-bottom:1px solid #888;">
                    PO. No. <?=$det->po_number?>, INVOICE NO. <?=$invoices->invoice_number?>
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align:left;vertical-align:middle;padding:4px 0;border-bottom:1px solid #888;" colspan="3">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align:left;vertical-align:middle;padding:4px 0;border-bottom:1px solid #888;" colspan="3">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align:left;vertical-align:middle;padding:4px 0;border-bottom:1px solid #888;" colspan="3">
                    &nbsp;
                </td>
            </tr>
        </table>
         <table border="0px" cellpadding="5" cellspacing="5" style="width:100%;">
            <tr>
                <td style="width:50%;text-align:left;vertical-align:middle;padding:5px 0;">
                    <table border="0px" style="width:70%">
                        <tr>
                            <th style="text-align:right;width:30%;padding:5px 2px;border-top:2px solid #111;border-bottom:2px solid #111;font-size:22;">
                                Rp.    
                            </th>
                            <th style="text-align:right;width:65%;padding:5px;background:#bbb;border-top:2px solid #111;border-bottom:2px solid #111;font-size:22;">
                                <?=format_currency_indo($invoices_amount->invoice_total)?>
                            </th>
                        </tr>
                        
                    </table>
                    
                </td>
                <td style="width:50%;text-align:center;vertical-align:middle;padding:5px 0;">
                    <table border="0px" style="width:100%">
                        <tr>
                        <td style="text-align:center;width:40%;padding:5px 2px;border-top:2px;font-size:20;font-family:verdana">
                            
                                Bandung, 
                                <?php
                                    $tgl=date('d',strtotime($invoices->invoice_date_created));
                                    $bln=bulan(date('n',strtotime($invoices->invoice_date_created)));
                                    $thn=date('Y',strtotime($invoices->invoice_date_created));
                                    echo $tgl.' '.$bln.' '.$thn;
                                ?>
                                <br><br><br>
                                <br><br>
                                <br><br><br>
                                <br><br>
                                <br><br><br>
                                (_____________________________)
                                <br><br>
                                <br><br><br>

                            
                        </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>