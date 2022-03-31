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
<header class="clearfix" style="padding-top:-60px;margin-top:-60px;margin-bottom:10px;">
    <h3 style="text-align:center;font-size:14px;">DELIVERY ORDER (DO)</h3>
    <div class="invoice-details clearfix" style="width:100%;float:left;padding-top:30px;">
        <div style="width:30%;float:left;text-align:left;">
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
        <div style="width:20%;float:right;text-align:right"></div>
        
        <div style="width:30%;float:left;text-align:left;">
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
        <div style="width:10%;float:right;text-align:right"></div>
        
        <div style="width:30%;float:right;text-align:right;padding-left:40px;">
            <table style="width:100%;float:right;;">
                <tr>
                    <td style="width:50%;vertical-align:top;">DELIVERY NO</td>
                    <td style="width:5px;vertical-align:top;"><?php echo ': '; ?></td>
                    <td style="width:50%;vertical-align:top;padding-left:20px;"><?=$dorder->do_number?></td>
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
            <th class="item-name">P.O. Date</th>
            <th class="item-name">P.O. Number</th>
            <th class="item-desc">BUYER</th>
            <th class="item-amount">Driver</th>
            <th class="item-total">Delivery Terms</th>
        </tr>
        <tr>
            <td class="item-name"><?=date('d/m/Y',strtotime($det->po_date))?></td>
            <td class="item-name"><?=($det->po_number)?></td>
            <td class="item-desc"><?=$dorder->do_buyer?></td>
           <td class="item-amount"> </td> <!-- <?=$dorder->do_fob?>-->
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