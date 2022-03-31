<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title>Delivery Order (DO)</title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
       
</head>
<body style="padding-top:0px;margin-top:0px;">
<header class="clearfix" style="padding-top:-60px;margin-top:-60px;margin-bottom:10px;">
    <h3 style="text-align:center;font-size:14px;">DELIVERY ORDER (DO)</h3>
    <div class="invoice-details clearfix" style="width:100%;float:left;padding-top:30px;">
        <div style="width:50%;float:left;text-align:left;">
            SHIP TO :<br>
            
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
        <div style="width:40%;float:right;text-align:right;padding-left:40px;">
            <table style="width:100%;float:right;;">
                <tr>
                    <td style="width:40%;vertical-align:top;">DELIVERY NO</td>
                    <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                    <td style="width:53%;vertical-align:top;padding-left:20px;"><?=$dorder->do_number?></td>
                </tr>
                <tr>
                    <td style="width:40%;vertical-align:top;">REF. ORDER NO</td>
                    <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                    <td style="width:53%;vertical-align:top;padding-left:20px;"><?=(isset($invoices->invoice_number) ? $invoices->invoice_number : (isset($det->po_number) ? $det->po_number : '_________________'))?></td>
                </tr>
                <tr>
                    <td style="width:40%;vertical-align:top;">DRIVER</td>
                    <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                    <td style="width:53%;vertical-align:top;padding-left:20px;"><?=$dorder->do_fob?></td>
                </tr>
                <tr>
                    <td style="width:40%;vertical-align:top;">REQUEST NO</td>
                    <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                    <td style="width:53%;vertical-align:top;padding-left:20px;">_________________</td>
                </tr>
                <tr>
                    <td style="width:40%;vertical-align:top;">DELIVERY DATE</td>
                    <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                    <td style="width:53%;vertical-align:top;padding-left:20px;"><?=date('d/m/Y',strtotime($dorder->do_ship_date))?></td>
                </tr>
            </table>
        </div>
    </div>

    
</header>

<main>
    
    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name">No</th>
            <th class="item-name"><?php _trans('Product Code'); ?></th>
            <th class="item-name"><?php _trans('Product Name'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-amount text-right"><?php _trans('UOM'); ?></th>
            <th class="item-total text-right">REMARK</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $no=1;
        foreach ($items as $item) { ?>
            <tr>
                <td><?=$no?></td>
                <td><?php _htmlsc($item->item_product_id); ?></td>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td class="text-center">
                    <?php echo format_amount($item->item_quantity); ?>
                </td>
                <td class="text-center">
                    <?php if ($item->item_product_unit) : ?>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                  
                </td>
            </tr>
        <?php 
        $no++;
        } ?>

        </tbody>
    </table>

</main>

<footer style="border-top:0px !important;padding-top:70px;">
    <div style="width:100%;float:left">
        <div style="width:25%;float:left;text-align:center">PREPARED BY, 
            <br>
            <br>
            <br>
            <br>
            (______________________)
        </div>
        <div style="width:25%;float:left;text-align:center">APPROVED BY,
            <br>
            <br>
            <br>
            <br>
            (______________________)
        </div>
        <div style="width:25%;float:left;text-align:center">DELIVERED BY,
            <br>
            <br>
            <br>
            <br>
            (______________________)
        </div>
        <div style="width:25%;float:left;text-align:center">RECEIVED BY,
            <br>
            <br>
            <br>
            <br>
            (______________________)
        </div>
    </div>

</footer>

    
    
</body>
</html>
