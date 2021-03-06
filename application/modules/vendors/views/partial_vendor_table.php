<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php _trans('active'); ?></th>
            <th><?php _trans('vendor_name'); ?></th>
            <th><?php _trans('email_address'); ?></th>
            <th><?php _trans('phone_number'); ?></th>
            <th class="amount"><?php _trans('balance'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $vendor) : ?>
            <tr>
                <td><?php echo ($vendor->vendor_active) ? trans('yes') : trans('no'); ?></td>
                <td><?php echo anchor('vendors/view/' . $vendor->vendor_id, htmlsc(format_vendor($vendor))); ?></td>
                <td><?php _htmlsc($vendor->vendor_email); ?></td>
                <td><?php _htmlsc($vendor->vendor_phone ? $vendor->vendor_phone : ($vendor->vendor_mobile ? $vendor->vendor_mobile : '')); ?></td>
                <td class="amount"><?php echo format_currency($vendor->vendor_invoice_balance); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('vendors/view/' . $vendor->vendor_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('vendors/form/' . $vendor->vendor_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="vendor-create-quote"
                                   data-vendor-id="<?php echo $vendor->vendor_id; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="vendor-create-invoice"
                                   data-vendor-id="<?php echo $vendor->vendor_id; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php _trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('vendors/delete/' . $vendor->vendor_id); ?>"
                                   onclick="return confirm('<?php _trans('delete_vendor_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
