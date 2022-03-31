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
<h3 style="text-align:center;font-size:14px;">INVOICE</h3>
<table style="margin-left:-4px;">
            <tr>
                <td><?php echo trans('No. '); ?></td>
                <td>:</td>
                <td><?php echo $invoice->invoice_number; ?></td>
            </tr>
            <!--<tr>
                <td><?php echo trans('Invoice Date'); ?></td>
                <td>:</td>
                <td><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
                
            </tr>
            <tr>
                <td><?php echo trans('due_date') . ': '; ?></td>
                <td><?php echo date_from_mysql($invoice->invoice_date_due, true); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('amount_due') . ': '; ?></td>
                <td><?php echo format_currency($invoice->invoice_balance); ?></td>
            </tr>
            <?php if ($payment_method): ?>
                <tr>
                    <td><?php echo trans('payment_method') . ': '; ?></td>
                    <td><?php _htmlsc($payment_method->payment_method_name); ?></td>
                </tr>
            <?php endif; ?>-->
        </table>
        <br>
<header class="clearfix">

    <?php
    if($invoice->vendor_id!=0)
    {
        $vendor=$this->db->from('ip_vendors')->where('vendor_id',$invoice->vendor_id)->get()->row();
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
            <b><?php echo 'To : '; ?> <?php echo $custom_fields['client']['Contact Person Name'] ?></b><br>
            <b><?php _htmlsc($invoice->client_name); ?></b>
        </div>
        <?php 
        if ($invoice->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . $invoice->client_vat_id . '</div>';
        }
        if ($invoice->client_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . $invoice->client_tax_code . '</div>';
        }
        if ($invoice->client_address_1) {
            echo '<div>' . htmlsc($invoice->client_address_1) . '</div>';
        }
        if ($invoice->client_address_2) {
            echo '<div>' . htmlsc($invoice->client_address_2) . '</div>';
        }
        if ($invoice->client_city || $invoice->client_state || $invoice->client_zip) {
            echo '<div>';
            if ($invoice->client_city) {
                echo htmlsc($invoice->client_city) . ' ';
            }
            if ($invoice->client_state) {
                echo htmlsc($invoice->client_state) . ' ';
            }
            if ($invoice->client_zip) {
                echo htmlsc($invoice->client_zip);
            }
            echo '</div>';
        }
        if ($invoice->client_state) {
            echo '<div>' . htmlsc($invoice->client_state) . '</div>';
        }

        if ($invoice->client_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($invoice->client_phone) . '</div>';
        } ?>

    </div>
<?php
    }
    ?>
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
</footer>
<div style="margin-left:450px; margin-top:-20px;">
    <p>
        <b>Bekasi, <?php echo date_from_mysql($invoice->invoice_date_created, true); ?></b>
    </p>
    <img src="https://jedanka.com/wp-content/uploads/2018/05/ttd-nicho.png" style="max-width:230px; margin-bottom:-40px; margin-top:-30px;">
    <p>
        Nicho Alhadad
    </p>
    <hr style="text-align:left; width:180px; margin-top:-15px;">
    <p style="margin-top:-15px;">
        <i >Manager Sales</i>
    </p>
</div>
</body>
</html>
