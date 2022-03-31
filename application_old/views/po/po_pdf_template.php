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
<h3 style="text-align:center;font-size:14px;">PURCHASE ORDER (PO)</h3>
<header class="clearfix" style="margin:0px; padding:0px;margin-top:10px;padding-top:10px;">
    <div class="invoice-details clearfix" style="margin:0px; padding:0px;">
        <div style="width:100%;float:left">
        <?php
        if($det->supplier_id!=0)
        {
        ?>
            <div style="width:32%;float:left;text-align:left;font-size:11px;font-family:arial;padding-right:10px;">
                SUPPLIER/VENDOR<br>
                <b>
                <?php echo $suppliers[$det->supplier_id]->supplier_name;?>
                </b><br>
                <?php echo $suppliers[$det->supplier_id]->supplier_address_1;?><br>
                <?php echo $suppliers[$det->supplier_id]->supplier_state;?><br>
            </div>
        <?php
        }
        ?>
            <div style="width:32%;float:left;text-align:left;font-size:11px;font-family:arial;padding-right:10px;">
                SHIP TO<br>
            
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
                
            </div>
            <div style="width:32%;float:left;text-align:left;font-size:11px;font-family:arial;">
                BILL TO<br>
                <b>PT. JEDANKA GLOBAL SINERGI</b><br>
                Jababeka Innovation Center, Pintu 6<br>
                Jl. Samsung 2C Blok C2T, Jababeka<br>
                Bekasi - Indonesia
                
            </div>
        </div>
    </div>
    <br>

</header>

<main>
    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-price text-right"><?php _trans('price'); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th class="item-discount text-right"><?php _trans('discount'); ?></th>
            <?php endif; ?>
            <th class="item-total text-right"><?php _trans('total'); ?></th>
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
            <td class="text-right clear-border" colspan="3">&nbsp;</td>
            <td class="text-right"><?php _trans('subtotal'); ?></td>
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

        <?php if ($quote->quote_item_tax_total > 0) { ?>
            <tr>
                <td class="text-right clear-border" colspan="3">&nbsp;</td>
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

                <td class="text-right clear-border" colspan="3">&nbsp;</td>
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
                <td colspan="3" class="clear-border text-right">&nbsp;</td>
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
                <td colspan="3" class="clear-border text-right">&nbsp;</td>
                <td class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="3" class="clear-border text-right">&nbsp;</td>
            <td class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($quote->quote_total); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

</main>
<div style="width:60%;float:left;">
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
</div>
<footer style="border-top:0px !important;padding-top:10px;">
    <div style="width:100%;float:left">
        <div style="width:70%;float:left"></div>
        <div style="width:30%;float:right;text-align:center !important;color:#000 !important;">
            <b>PT. JEDANKA GLOBAL SINERGI</b>
            
            <!-- <img src="https://jedanka.com/wp-content/uploads/2018/05/ttd-nicho.png" style="max-width:230px; margin-bottom:-40px;"> -->
            
            <p style="text-align:center !important;padding-top:30px;">
                Jemiro Kasih, S.T., MMSI
                <hr style="text-align:left; width:180px; margin-top:-15px;padding:0px !important;" />
                <div style="margin-top:-15px;">Director</div>
            </p>
        </div>

    </div>

</footer>

    
    
</body>
</html>
