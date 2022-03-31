<div class="table-responsive">
    <table id="item_table" class="items table table-condensed table-bordered no-margin" border="1">
        <thead>
        <tr>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111">No</th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('item'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('description'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111"><?php _trans('quantity'); ?></th>
            <th style="text-align:center;padding:10px;background:#ddd;border:1px solid #111">UOM</th>
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
                
            </tr>
            
        <?php 
            $total+=($item->item_price * $item->item_quantity);
            $no++;
        } ?>
           
        </tbody>

    </table>
</div>

<br>
