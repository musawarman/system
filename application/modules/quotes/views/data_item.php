<div class="table-responsive">
    <table id="item_table" class="items table table-condensed table-bordered no-margin" border="1">
        <thead>
        <tr>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111">No</th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('item'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('description'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('quantity'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111">UOM</th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('price'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('total'); ?></th>
        </tr>
        </thead>

  
        <tbody class="item">
        <?php 
            $no=1;
            $total=0;
            foreach ($items as $item) { ?>
            <tr>
                <td class="td-icon" style="vertical-align:top;text-align:center"><?=$no?></td>
                <td class="td-text" style="vertical-align:top">
                        <?php _htmlsc($item->item_name); ?>
                </td>
                <td class="td-amount td-quantity text-left" style="text-align:left;">
                        <?php echo trim(nl2br($item->item_description)); ?>
                </td>
                <td class="td-amount td-quantity text-center" style="text-align:center;">
                    <?php echo format_amount($item->item_quantity); ?>
                </td>
                <td class="td-amount td-quantity text-center" style="text-align:center;">
                    <?php echo $item->item_product_unit;?>
                </td>
                <td class="td-amount text-right" style="text-align:right;">
                    <?php echo format_currency_indo($item->item_price); ?>
                </td>
                <td class="td-amount text-right" style="text-align:right;">
                    <?php echo format_currency_indo($item->item_price * $item->item_quantity); ?>
                </td>
                
            </tr>
            
        <?php 
            $total+=($item->item_price * $item->item_quantity);
            $no++;
        } ?>
           
        </tbody>

    </table>
</div>

<br>

<div class="row">
    <div class="col-xs-12 col-md-4">
       &nbsp;
    </div>

    <div class="col-xs-12 visible-xs visible-sm"><br></div>

    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-bordered text-right">
            <tr>
                <td style="width: 40%;"><?php _trans('subtotal'); ?></td>
                <td style="width: 60%;" class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
            </tr>
            <?php if ($quote_tax_rates) {
                    foreach ($quote_tax_rates as $quote_tax_rate) { ?>
            <tr>
                <td>
                <?php echo htmlsc($quote_tax_rate->quote_tax_rate_name) . ' ' . format_amount($quote_tax_rate->quote_tax_rate_percent); ?>
                                % (PPN)</span></td>
                <td>
                    
                            <span class="amount">
                                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                            </span>
                        <?php }
                     ?>
                </td>
            </tr>
            <?php }?>
            <tr>
                <td><b><?php _trans('total'); ?></b></td>
                <td class="amount"><b><?php echo format_currency($quote->quote_total); ?></b></td>
            </tr>
        </table>
    </div>

</div>
