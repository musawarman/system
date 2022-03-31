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
<header class="clearfix">
<h3 style="text-align:center;font-size:14px;">QUOTATION</h3>
   <!-- <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>-->
    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td><?php echo trans('Quote No. '); ?></td>
                <td><?php echo ': '; ?></td>
                <td><?php echo $quote->quote_number; ?></td>
            </tr>
            <tr>
                <td><?php echo trans('Quote Date '); ?></td>
                <td><?php echo ': '; ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
            </tr>
           <tr>
                <td><?php echo trans('Expires '); ?></td>
                <td><?php echo ': '; ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_expires, true); ?></td>
            </tr>
        </table>
    </div>
    <br>
    <?php
    if($quote->vendor_id!=0)
    {
        $vendor=$this->db->from('ip_vendors')->where('vendor_id',$quote->vendor_id)->get()->row();
    ?>
        <div id="client">
            <div>
                <b><?php echo 'To : '; ?> <?php echo isset($custom_fields['vendor']['Contact Person Name']) ? $custom_fields['vendor']['Contact Person Name'] : '' ?></b><br>
                <b><?php _htmlsc($vendor->vendor_name); ?></b>
            </div>
            <?php 
            if ($vendor->vendor_vat_id) {
                echo '<div>' . trans('vat_id_short') . ': ' . $vendor->vendor_vat_id . '</div>';
            }
            if ($vendor->vendor_tax_code) {
                echo '<div>' . trans('tax_code_short') . ': ' . $vendor->vendor_tax_code . '</div>';
            }
            if ($vendor->vendor_address_1) {
                echo '<div>' . htmlsc($vendor->vendor_address_1) . '</div>';
            }
            if ($vendor->vendor_address_2) {
                echo '<div>' . htmlsc($vendor->vendor_address_2) . '</div>';
            }
            if ($vendor->vendor_city || $vendor->vendor_state || $vendor->vendor_zip) {
                echo '<div>';
                if ($vendor->vendor_city) {
                    echo htmlsc($vendor->vendor_city) . ' ';
                }
                if ($vendor->vendor_state) {
                    echo htmlsc($vendor->vendor_state) . ' ';
                }
                if ($vendor->vendor_zip) {
                    echo htmlsc($vendor->vendor_zip);
                }
                echo '</div>';
            }
            if ($vendor->vendor_state) {
                echo '<div>' . htmlsc($vendor->vendor_state) . '</div>';
            }

            if ($vendor->vendor_phone) {
                echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($vendor->vendor_phone) . '</div>';
            } ?>

        </div>
    <?php
    }
    else
    {
    ?>
    <div id="client">
        <div>
            <b><?php echo 'To : '; ?> <?php echo isset($custom_fields['client']['Contact Person Name']) ? $custom_fields['client']['Contact Person Name'] : '' ?></b><br>
            <b><?php _htmlsc($quote->client_name); ?></b>
        </div>
        <?php 
        if ($quote->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . $quote->client_vat_id . '</div>';
        }
        if ($quote->client_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . $quote->client_tax_code . '</div>';
        }
        if ($quote->client_address_1) {
            echo '<div>' . htmlsc($quote->client_address_1) . '</div>';
        }
        if ($quote->client_address_2) {
            echo '<div>' . htmlsc($quote->client_address_2) . '</div>';
        }
        if ($quote->client_city || $quote->client_state || $quote->client_zip) {
            echo '<div>';
            if ($quote->client_city) {
                echo htmlsc($quote->client_city) . ' ';
            }
            if ($quote->client_state) {
                echo htmlsc($quote->client_state) . ' ';
            }
            if ($quote->client_zip) {
                echo htmlsc($quote->client_zip);
            }
            echo '</div>';
        }
        if ($quote->client_state) {
            echo '<div>' . htmlsc($quote->client_state) . '</div>';
        }

        if ($quote->client_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->client_phone) . '</div>';
        } ?>

    </div>
    <?php
    }
    ?>
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
    
    <img src="https://jedanka.com/wp-content/uploads/2018/05/ttd-nicho.png" style="max-width:230px; margin-bottom:-40px;">
    <!-- <img src="http://system.jedanka.com/uploads/ttd-ansori.png" style="max-width:180px; margin-bottom:-40px;"> -->
    <div style="max-width:200px; margin-bottom:-40px;">&nbsp;</div>
     <p style="text-align:center;width:180px;font-size:16px;">
        <b>A n s o r i.</b>
        <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
        <hr style="text-align:left; width:180px; margin-top:-5px;">
        Direktur
    </p>
</footer>

    
    
</body>
</html>
