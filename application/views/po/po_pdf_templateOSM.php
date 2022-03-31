<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title>Purchase Order (PO)</title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
       
</head>
<body style="margin-top:0px; padding-top:0px;">
<header class="clearfix" style="padding-top:-60px;margin-top:-60px;margin-bottom:10px;">
<h3 style="text-align:center;font-size:14px;">PURCHASE ORDER (PO)</h3>
<div class="invoice-details clearfix" style="width:100%;float:left;padding-top:30px;">
    <table border="0" style="width:100%">
        <tr>
            <td style="width:40%">
            Sir/Madam<br>

                <?php
                    if($quotes->vendor_id!=0)
                    {
                        echo '<b>'.$vendors[$quotes->vendor_id]->vendor_name.'</b><br>';
                        echo $vendors[$quotes->vendor_id]->vendor_address_1.'<br>';
                        echo $vendors[$quotes->vendor_id]->vendor_state.'<br>';
                    }
                    else
                    {
                        echo '<b>'.$clients[$quotes->client_id]->client_name.'</b><br>';
                        echo $clients[$quotes->client_id]->client_address_1.'<br>';
                        echo $clients[$quotes->client_id]->client_state.'<br>';
                    }
                ?>  
            <!-- <b>HEAD OFFICE :</b><br>
            Bukit Sukasari Residence Blok<br>
            F.01 No. 12 Sukasari Purwasari<br>
            Karawang Jawa Barat 37117<br>
            NPWP : 74.965.315.0-433.000<br> -->
            </td>
            <td style="width:30%">&nbsp;</td>
            <td style="width:30%"><b>IMPORTANT :</b><br>
                1. Please Sign and Email of Copy PO<br>
                2. Please Attach Copy of PO together<br>
                with the invoice for setting payment.
                3. Please Indicate PO Number on Bill<br>
                </td>
        </tr>
        <tr>
            <td style="width:40%">

            </td>
            <td style="width:30%">&nbsp;</td>
            <td style="width:30%">
            Our Order : <?=$det->po_number?><br>
            Our Date :  <?=date('d/m/Y',strtotime($det->po_date))?><br>
            Rev : ___
            </td>
        </tr>
    </table>
   </div> 

</header>

<main>
    <p>Dear Sirs,<br>
We Hereby ask you to deliver the following goods in accordance with our terms of delivery</p>
    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name">No</th>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-amount text-right">Unit</th>
            <th class="item-price text-right"><?php _trans('price'); ?></th>
           
            <th class="item-total text-right"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $no=1;
        foreach ($items as $item) { ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                </td>
                <td class="text-right">
                    <?php echo ($item->item_product_unit); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
               
                <td class="text-right">
                    <?php echo format_currency($item->item_price * $item->item_quantity); ?>
                </td>
            </tr>
        <?php 
        $no++;
        } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td class="text-right clear-border" colspan="5">&nbsp;</td>
            <td class="text-right"><?php _trans('subtotal'); ?></td>
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

        <?php if ($quote->quote_item_tax_total > 0) { ?>
            <tr>
                <td class="text-right clear-border" colspan="5">&nbsp;</td>
                <td  class="text-right">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
            <tr>

                <td class="text-right clear-border" colspan="5">&nbsp;</td>
                <td class="text-right">
                    <?php echo $quote_tax_rate->quote_tax_rate_name . ' (' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if ($quote->quote_discount_percent != '0.00') : ?>
            <tr>
                <td colspan="5" class="clear-border text-right">&nbsp;</td>
                <td class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($quote->quote_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($quote->quote_discount_amount != '0.00') : ?>
            <tr>
                <td colspan="5" class="clear-border text-right">&nbsp;</td>
                <td class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="5" class="clear-border text-right">&nbsp;</td>
            <td class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($quote->quote_total); ?></b>
            </td>
        </tr>
        </tbody>
    </table>
    <p>
        After you received our PO, Please send us the confirmation order to Email : bintangzahrakrw@gmail.com<br/>
        Delivery :<br/>
        Payment 30 Day After Delivery<br/>
        Please state our order and item number on all invoice
        <br><br>
        This Purchase Order is issued electronically by CV. Bintang Zahra, as such it does not need<br>
        any signature or stamps from CV. Bintang Zahra to be put there of''
    </p>
</main>
<!-- <div style="width:60%;float:left;">
    <div style="width:100%;float:left">
        <div style="padding:1px 2px;float:left;width:40%;background:#d4e1f4;border:1px solid #4687f2">Bank Name</div>
        <div style="padding:1px 2px;float:left;width:55%;border:1px solid #4687f2;background:#f7f9fc"><?=$det->po_bank_name?>&nbsp;</div>
    </div>
    <div style="width:100%;float:left">
        <div style="padding:1px 2px;float:left;width:40%;background:#d4e1f4;border:1px solid #4687f2">Account Number</div>
        <div style="padding:1px 2px;float:left;width:55%;border:1px solid #4687f2;background:#f7f9fc"><?=$det->po_account_number?>&nbsp;</div>
    </div>
    <div style="width:100%;float:left">
        <div style="padding:1px 2px;float:left;width:40%;background:#d4e1f4;border:1px solid #4687f2">Payment Terms</div>
        <div style="padding:1px 2px;float:left;width:55%;border:1px solid #4687f2;background:#f7f9fc"><?=$det->po_payment_term?>&nbsp;</div>
    </div>
    <div style="width:100%;float:left">
        <div style="padding:1px 2px;float:left;width:40%;background:#d4e1f4;border:1px solid #4687f2">Freight Terms</div>
        <div style="padding:1px 2px;float:left;width:55%;border:1px solid #4687f2;background:#f7f9fc"><?=$det->po_freight_term?>&nbsp;</div>
    </div>
    <div style="width:100%;float:left">
        <div style="padding:1px 2px;float:left;width:40%;background:#d4e1f4;border:1px solid #4687f2">Price Basic</div>
        <div style="padding:1px 2px;float:left;width:55%;border:1px solid #4687f2;background:#f7f9fc"><?=$det->po_price_basic?>&nbsp;</div>
    </div>
    <div style="width:100%;float:left">
        <div style="padding:1px 2px;float:left;width:40%;background:#d4e1f4;border:1px solid #4687f2">Address All Queries To</div>
        <div style="padding:1px 2px;float:left;width:55%;border:1px solid #4687f2;background:#f7f9fc"><?=$det->po_address_all_queries?>&nbsp;</div>
    </div>
</div> -->
<footer style="border-top:0px !important;padding-top:10px;">
    <table border="0">
            <tr>
                <td style="width:25%;text-align:center">
                    Approved by,<br>
                   <img src="http://system.jedanka.com/uploads/ttd-ansori.png" style="max-width:180px; margin-bottom:-40px;">
                    <div style="max-width:200px; margin-bottom:-20px;">&nbsp;</div>
                    <p style="text-align:center;width:180px;font-size:13px;">
                        <br>
                        
                        <b>Anshori ST, Mr.</b>
                        <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                        <hr style="text-align:left; width:180px; margin-top:5px;">
                        Direktur
                    </p>

                </td>
                <td style="width:50%">&nbsp;</td>
                <td style="width:25%;text-align:center">
                    Signed on behalf seller<br>
                    
                    <div style="max-width:200px; margin-bottom:-20px;">&nbsp;</div>
                    <p style="text-align:center;width:180px;font-size:13px;">
                        <br>
                        <br>
                        <br>
                        
                        <b>&nbsp;</b>
                        <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                        <hr style="text-align:left; width:180px; margin-top:5px;">
                        Stamps & Signature
                    </p>
                </td>
            </tr>
    </table>
    <!-- <div style="width:100%;float:left">
        <div style="width:70%;float:left"></div>
        <div style="width:30%;float:right;text-align:center !important;color:#000 !important;">
            <b>PT. JEDANKA GLOBAL SINERGI</b>
            
           <img src="https://jedanka.com/wp-content/uploads/2018/05/ttd-nicho.png" style="max-width:230px; margin-bottom:-40px;"> 
            
            <p style="text-align:center !important;padding-top:30px;">
                Jemiro Kasih, S.T., MMSI
                <hr style="text-align:left; width:180px; margin-top:-15px;padding:0px !important;" />
                <div style="margin-top:-15px;">Director</div>
            </p>
        </div>

    </div> -->

</footer>

    
    
</body>
</html>
