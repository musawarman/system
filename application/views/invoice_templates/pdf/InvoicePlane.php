<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('invoice'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
</head>
<body>

<header class="clearfix" style="padding-top:-60px;margin-top:-60px;margin-bottom:10px;">
    <h3 style="text-align:center;font-size:14px;">INVOICE</h3>
    <div class="invoice-details clearfix" style="width:100%;float:left;padding-top:30px;">
        <div style="width:50%;float:left;text-align:left">
            <?php
                if($invoice->vendor_id!=0)
                {
                    $vendor=$this->db->from('ip_vendors')->where('vendor_id',$invoice->vendor_id)->get()->row();
                ?>
                    <table style="width:100%">
                        <tr>
                            <td style="width:35%;vertical-align:top;">To</td>
                            <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="width:63%;vertical-align:top;">
                            <?php _htmlsc($vendor->vendor_name); ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Address</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;"><?php
                            if (isset($vendor->vendor_address_1)) {
                                echo $vendor->vendor_address_1;
                            }
                            ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Attention</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;"><?php echo isset($custom_fields['vendor']['Contact Person Name']) ? $custom_fields['vendor']['Contact Person Name'] : '' ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Email</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;"><?php
                            if (isset($vendor->vendor_email)) {
                                echo $vendor->vendor_email;
                            }
                            ?></td>
                        </tr>
                    </table>
                    
                <?php
                }
                else
                {
                ?>
                
                     <table style="width:100%">
                        <tr>
                            <td style="width:25%;vertical-align:top;">To</td>
                            <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="width:73%;vertical-align:top;padding-left:20px;"><?php _htmlsc($invoice->client_name); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Address</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;padding-left:20px;">
                            <?php
                            if (isset($invoice->client_address_1)) {
                                echo htmlsc($invoice->client_address_1);
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Attention</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;padding-left:20px;"><?php echo isset($custom_fields['client']['Contact Person Name']) ? $custom_fields['client']['Contact Person Name'] : '' ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Email</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;padding-left:20px;"><?php
                            if (isset($client->client_email)) {
                                echo $client->client_email;
                            }
                            ?></td>
                        </tr>
                    </table>
                    
                
                <?php
                }
                ?>
        </div>
        
        <div style="width:10%;float:right;text-align:left"> </div>
        <div style="width:40%;float:right;text-align:left">
            <table style="width:100%">
                <tr>
                    <td style="width:45%"><?php echo trans('Invoice No. '); ?></td>
                    <td style="width:5px"><?php echo ': '; ?></td>
                    <td style="width:45%;padding-left:20px;"><?php echo $invoice->invoice_number; ?></td>
                </tr>
                <tr>
                    <td><?php echo trans('Invoice Date '); ?></td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:20px;"><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
                </tr>
                <tr>
                    <td><?php echo trans('due_date') . ': '; ?></td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:20px;"><?php echo date_from_mysql($invoice->invoice_date_due, true); ?></td>
                </tr>
                <tr>
                    <td><?php echo trans('PO No.') . ': '; ?></td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:20px;"><?=(isset($po->po_number) ? $po->po_number : '')?></td>
                </tr>
                <tr>
                    <td><?php echo trans('Rev Delivery Order') . ': '; ?></td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:20px;"><?=(isset($do->do_number) ? $do->do_number : '')?></td>
                </tr>
            </table>
        </div>
    </div>
</header>
<main>


    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php _trans('Barang / Jasa'); ?></th>
            <th class="item-desc"><?php _trans('Deskripsi'); ?></th>
            <th class="item-amount text-right"><?php _trans('Banyak'); ?></th>
            <th class="item-price text-right"><?php _trans('Harga'); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th class="item-discount text-right"><?php _trans('Diskon'); ?></th>
            <?php endif; ?>
            <th class="item-total text-right"><?php _trans('Total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                    <?php if ($item->item_product_unit) : ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
                <?php if ($show_item_discounts) : ?>
                    <td class="text-right">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right">
                    <?php echo format_currency($item->item_total); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

             
        <tr>
            <td <?php echo(isset($show_discounts) ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php echo trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
        </tr>

        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo(isset($show_discounts) ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
            <tr>
                <td <?php echo(isset($show_discounts) ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo $invoice_tax_rate->invoice_tax_rate_name . ' (' . $invoice_tax_rate->invoice_tax_rate_percent . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <td <?php echo(isset($show_discounts) ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php echo trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
       <!-- <tr>
            <td <?php echo(isset($show_discounts) ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php echo trans('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($invoice->invoice_paid); ?>
            </td>
        </tr>
        <tr>
            <td <?php echo(isset($show_discounts) ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php echo trans('balance'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_balance); ?></b>
            </td>
        </tr>-->
        </tbody>

    </table>

 <?php 


        $angka = $invoice->invoice_balance;
        echo "<p style='font-size:14px;'>Terbilang : <b><i>" . terbilang($angka) . " Rupiah</i></b></p>";
        ?>

</main>
<br>
<footer style="margin-top:-30px; font-size:11px;">
    
    <?php if ($invoice->invoice_terms) : ?>
        <div class="notes">
            <br>
            <b><?php _trans('AKUN BANK :'); ?></b><br/>
            Pembayaran atas tagihan ini hanya ditujukan pada <b>PT Jedanka Global Sinergi</b><br>
            <b>Bank BNI - 0686730638</b>, Cabang Purwakarta a/n <b>Jedanka Global Sinergi PT</b><br>
        </div>
        <br>
        <div class="notes">
            <b><?php _trans('CATATAN :'); ?></b><br/>
            <?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
        </div>
    <?php endif; ?>
    <table border="0" style="width:100%">
        <tr>
            <td style="width:70%">&nbsp;</td>
            <td style="width:30%;text-align:center">
                <p>
                    <b>Bekasi, <?php echo date_from_mysql($invoice->invoice_date_created, true); ?></b>
                </p>
                <img src="http://system.jedanka.com/uploads/ttdbudi.png" style="max-width:230px; margin-bottom:-40px; margin-top:-30px;"> -->
                <div style="max-width:230px; margin-bottom:-40px; margin-top:-30px;height:100px">&nbsp;</div>
                <br><br>
                Budi Septiawan<br>
                <hr style="text-align:left; width:180px; ">
                    <i >Director</i>
            </td>    
            
        </tr>
    </table>
</footer>
<!-- <div style="margin-left:450px; margin-top:-20px;"> -->
<!-- </div> -->
</body>
</html>
