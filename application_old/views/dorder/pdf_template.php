<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title>Delivery Order (DO)</title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
       
</head>
<body style="margin-top:0px; padding-top:0px;">
<header class="clearfix" style="margin:0px; padding:0px;">

    <div class="invoice-details clearfix" style="margin:0px; padding:0px;">
        <div style="width:100%;float:left">
            <div style="width:32%;float:left;text-align:left;font-size:11px;font-family:arial;">
                BILL TO :<br>
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
            <div style="width:32%;float:left;text-align:left;font-size:11px;font-family:arial;padding-right:10px;">
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
            
            <div style="width:32%;float:left;text-align:left;font-size:11px;font-family:arial;">
                D.O. Number :<br>
                <?=$invoices->do_number?>
            </div>
        </div>
    </div>
    <br>

</header>

<main>
    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name">P.O. Date</th>
            <th class="item-name">P.O. Number</th>
            <th class="item-desc">BUYER</th>
            <th class="item-amount">F.O.B</th>
            <th class="item-total">Delivery Terms</th>
        </tr>
        <tr>
            <td class="item-name"><?=date('d/m/Y',strtotime($det->po_date))?></td>
            <td class="item-name"><?=($det->po_number)?></td>
            <td class="item-desc"><?=$dorder->do_buyer?></td>
            <td class="item-amount"><?=$dorder->do_fob?></td>
            <td class="item-total"><?=$dorder->do_delivery_term?></td>
        </tr>
        <tr>
            <th class="item-name">Ship Date</th>
            <th class="item-name">Sales Person</th>
            <th class="item-desc">Shipped Via</th>
            <th class="item-amount"></th>
            <th class="item-total"></th>
        </tr>
        <tr>
            <td class="item-name"><?=date('d/m/Y',strtotime($dorder->do_ship_date))?></td>
            <td class="item-name"><?=$dorder->do_sales_person?></td>
            <td class="item-desc"><?=$dorder->do_shipped_via?></td>
            <td class="item-amount"></td>
            <td class="item-total"></td>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name">No</th>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-total text-right">REMARK</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $no=1;
        foreach ($items as $item) { ?>
            <tr>
                <td><?=$no?></td>
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
        <div style="width:29%;float:left">Delivered By : </div>
        <div style="width:20%;float:left">____ / ____ / __________</div>
        <div style="width:29%;float:left">Received By : </div>
        <div style="width:20%;float:left">____ / ____ / __________</div>
    </div>

</footer>

    
    
</body>
</html>
