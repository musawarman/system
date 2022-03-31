<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('quote'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
</head>
<body>
<header class="clearfix" style="padding-top:-60px;margin-top:-60px;margin-bottom:10px;">
    <h3 style="text-align:center;font-size:14px;">QUOTATION</h3>
    <div class="invoice-details clearfix" style="width:100%;float:left;padding-top:30px;">
        <div style="width:50%;float:left;text-align:left">
            <?php
                if($quote->vendor_id!=0)
                {
                    $vendor=$this->db->from('ip_vendors')->where('vendor_id',$quote->vendor_id)->get()->row();
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
                            <td style="width:73%;vertical-align:top;padding-left:30px;"><?php _htmlsc($quote->client_name); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Address</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;padding-left:30px;">
                            <?php
                            if (isset($quote->client_address_1)) {
                                echo htmlsc($quote->client_address_1);
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Attention</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;padding-left:30px;"><?php echo isset($custom_fields['client']['Contact Person Name']) ? $custom_fields['client']['Contact Person Name'] : '' ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;">Email</td>
                            <td style="vertical-align:top;"><?php echo ': '; ?></td>
                            <td style="vertical-align:top;padding-left:30px;"><?php
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
        <div style="width:40%;float:right;text-align:left;">
            <table style="width:100%;padding-left:90px;">
                <tr>
                    <td style="width:40%"><?php echo trans('Quote No. '); ?></td>
                    <td style="width:5px"><?php echo ': '; ?></td>
                    <td style="width:58%;padding-left:30px;"><?php echo $quote->quote_number; ?></td>
                </tr>
                <tr>
                    <td><?php echo trans('Quote Date '); ?></td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:30px;"><?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
                </tr>
            <tr>
                    <td>From</td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:30px;">Agus Setiawan</td>
                </tr>
            <tr>
                    <td>Page</td>
                    <td><?php echo ': '; ?></td>
                    <td style="padding-left:30px;">'01</td>
                </tr>
            </table>
        </div>
        
    </div>
</header>

<main>
    <p style="text-align:justify;">We here with pleased to submit our proposal based on your requirement, as per following terms and specifications for your kind consideration.</p>
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
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?>
                    class="text-right"><?php _trans('subtotal'); ?></td>
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

        <?php if ($quote->quote_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo $quote_tax_rate->quote_tax_rate_name . ' (' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if ($quote->quote_discount_percent != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($quote->quote_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($quote->quote_discount_amount != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($quote->quote_total); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

</main>

<footer>
    <?php if ($quote->notes) : ?>
        <div class="notes">
            <b><?php _trans('Terms & Conditions '); ?></b><br/>
            <?php echo nl2br(htmlsc($quote->notes)); ?>
        </div>
    <?php endif; ?>
    
    <p>
        Purchase order from Buyer is non cancellable, any payment is non refundable and  goods delivered is non returnable.
    </p>
    <p>
        We thank you for the opportunity to quote and trust the above information will fulfill the requirement.
    </p>
    <br>
    <p>
        Thankyou & Best regards,
    </p>
    
    <table border="0">
        
        <tr>
            <td style="width:30%;text-align:center">
               <img src="http://system.jedanka.com/uploads/ttdagus.png" style="max-width:140px; margin-bottom:-40px;"> 
                <div style="max-width:200px; margin-bottom:-40px;">&nbsp;</div>
                <p style="text-align:center !important;padding-top:30px;">
                Agus Setiawan
                <hr style="text-align:left; width:180px;">
                <div style="margin-top:-15px;">Sales Manager</div>
                
               
                </p>            
            </td>    
            <td style="width:70%">&nbsp;</td>
        </tr>
    </table>
    <!-- <img src="https://jedanka.com/wp-content/uploads/2018/05/ttd-nicho.png" style="max-width:230px; margin-bottom:-40px;"> -->

</footer>

    
    
</body>
</html>
