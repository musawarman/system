<script>
    $(function () {
        $('#save_vendor_note').click(function () {
            $.post('<?php echo site_url('vendors/ajax/save_vendor_note'); ?>',
                {
                    vendor_id: $('#vendor_id').val(),
                    vendor_note: $('#vendor_note').val()
                }, function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful
                        $('.control-group').removeClass('error');
                        $('#vendor_note').val('');

                        // Reload all notes
                        $('#notes_list').load("<?php echo site_url('vendors/ajax/load_vendor_notes'); ?>",
                            {
                                vendor_id: <?php echo $vendor->vendor_id; ?>
                            }, function (response) {
                                <?php echo(IP_DEBUG ? 'console.log(response);' : ''); ?>
                            });
                    } else {
                        // The validation was not successful
                        $('.control-group').removeClass('error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().addClass('has-error');
                        }
                    }
                });
        });
    });
</script>

<?php
$locations = array();
foreach ($custom_fields as $custom_field) {
    if (array_key_exists($custom_field->custom_field_location, $locations)) {
        $locations[$custom_field->custom_field_location] += 1;
    } else {
        $locations[$custom_field->custom_field_location] = 1;
    }
}
?>

<div id="headerbar">
    <h1 class="headerbar-title"><?php _htmlsc(format_vendor($vendor)); ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a href="#" class="btn btn-default vendor-create-quote"
               data-vendor-id="<?php echo $vendor->vendor_id; ?>">
                <i class="fa fa-file"></i> <?php _trans('create_quote'); ?>
            </a>
            <a href="#" class="btn btn-default vendor-create-invoice"
               data-vendor-id="<?php echo $vendor->vendor_id; ?>">
                <i class="fa fa-file-text"></i> <?php _trans('create_invoice'); ?></a>
            <a href="<?php echo site_url('vendors/form/' . $vendor->vendor_id); ?>"
               class="btn btn-default">
                <i class="fa fa-edit"></i> <?php _trans('edit'); ?>
            </a>
            <a class="btn btn-danger"
               href="<?php echo site_url('vendors/delete/' . $vendor->vendor_id); ?>"
               onclick="return confirm('<?php _trans('delete_vendor_warning'); ?>');">
                <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
            </a>
        </div>
    </div>

</div>

<ul id="submenu" class="nav nav-tabs nav-tabs-noborder">
    <li class="active"><a data-toggle="tab" href="#vendorDetails"><?php _trans('details'); ?></a></li>
    <li><a data-toggle="tab" href="#vendorQuotes"><?php _trans('quotes'); ?></a></li>
    <li><a data-toggle="tab" href="#vendorInvoices"><?php _trans('invoices'); ?></a></li>
    <li><a data-toggle="tab" href="#vendorPayments"><?php _trans('payments'); ?></a></li>
</ul>

<div id="content" class="tabbable tabs-below no-padding">
    <div class="tab-content no-padding">

        <div id="vendorDetails" class="tab-pane tab-rich-content active">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">

                    <h3><?php _htmlsc(format_vendor($vendor)); ?></h3>
                    <p>
                        <?php $this->layout->load_view('vendors/partial_vendor_address'); ?>
                    </p>

                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">

                    <table class="table table-bordered no-margin">
                        <tr>
                            <th>
                                <?php _trans('language'); ?>
                            </th>
                            <td class="td-amount">
                                <?php echo ucfirst($vendor->vendor_language); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _trans('total_billed'); ?>
                            </th>
                            <td class="td-amount">
                                <?php echo format_currency($vendor->vendor_invoice_total); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _trans('total_paid'); ?>
                            </th>
                            <th class="td-amount">
                                <?php echo format_currency($vendor->vendor_invoice_paid); ?>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <?php _trans('total_balance'); ?>
                            </th>
                            <td class="td-amount">
                                <?php echo format_currency($vendor->vendor_invoice_balance); ?>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading"><?php _trans('contact_information'); ?></div>
                        <div class="panel-body table-content">
                            <table class="table no-margin">
                                <?php if ($vendor->vendor_email) : ?>
                                    <tr>
                                        <th><?php _trans('email'); ?></th>
                                        <td><?php _auto_link($vendor->vendor_email, 'email'); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($vendor->vendor_phone) : ?>
                                    <tr>
                                        <th><?php _trans('phone'); ?></th>
                                        <td><?php _htmlsc($vendor->vendor_phone); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($vendor->vendor_mobile) : ?>
                                    <tr>
                                        <th><?php _trans('mobile'); ?></th>
                                        <td><?php _htmlsc($vendor->vendor_mobile); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($vendor->vendor_fax) : ?>
                                    <tr>
                                        <th><?php _trans('fax'); ?></th>
                                        <td><?php _htmlsc($vendor->vendor_fax); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($vendor->vendor_web) : ?>
                                    <tr>
                                        <th><?php _trans('web'); ?></th>
                                        <td><?php _auto_link($vendor->vendor_web, 'url', true); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($custom_fields as $custom_field) : ?>
                                    <?php if ($custom_field->custom_field_location != 2) {
                                        continue;
                                    } ?>
                                    <tr>
                                        <?php
                                        $column = $custom_field->custom_field_label;
                                        $value = $this->mdl_vendor_custom->form_value('cf_' . $custom_field->custom_field_id);
                                        ?>
                                        <th><?php _htmlsc($column); ?></th>
                                        <td><?php _htmlsc($value); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="panel panel-default no-margin">

                        <div class="panel-heading"><?php _trans('tax_information'); ?></div>
                        <div class="panel-body table-content">
                            <table class="table no-margin">
                                <?php if ($vendor->vendor_vat_id) : ?>
                                    <tr>
                                        <th><?php _trans('vat_id'); ?></th>
                                        <td><?php _htmlsc($vendor->vendor_vat_id); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($vendor->vendor_tax_code) : ?>
                                    <tr>
                                        <th><?php _trans('tax_code'); ?></th>
                                        <td><?php _htmlsc($vendor->vendor_tax_code); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($custom_fields as $custom_field) : ?>
                                    <?php if ($custom_field->custom_field_location != 4) {
                                        continue;
                                    } ?>
                                    <tr>
                                        <?php
                                        $column = $custom_field->custom_field_label;
                                        $value = $this->mdl_vendor_custom->form_value('cf_' . $custom_field->custom_field_id);
                                        ?>
                                        <th><?php _htmlsc($column); ?></th>
                                        <td><?php _htmlsc($value); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <?php if ($vendor->vendor_surname != ""): //vendor is not a company ?>
                <hr>

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <?php _trans('personal_information'); ?>
                            </div>

                            <div class="panel-body table-content">
                                <table class="table no-margin">
                                    <tr>
                                        <th><?php _trans('birthdate'); ?></th>
                                        <td><?php echo format_date($vendor->vendor_birthdate); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _trans('gender'); ?></th>
                                        <td><?php echo format_gender($vendor->vendor_gender) ?></td>
                                    </tr>
                                    <?php if ($this->mdl_settings->setting('sumex') == '1'): ?>
                                        <tr>
                                            <th><?php _trans('sumex_ssn'); ?></th>
                                            <td><?php echo format_avs($vendor->vendor_avs) ?></td>
                                        </tr>

                                        <tr>
                                            <th><?php _trans('sumex_insurednumber'); ?></th>
                                            <td><?php _htmlsc($vendor->vendor_insurednumber) ?></td>
                                        </tr>

                                        <tr>
                                            <th><?php _trans('sumex_veka'); ?></th>
                                            <td><?php _htmlsc($vendor->vendor_veka) ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($custom_fields as $custom_field) : ?>
                                        <?php if ($custom_field->custom_field_location != 3) {
                                            continue;
                                        } ?>
                                        <tr>
                                            <?php
                                            $column = $custom_field->custom_field_label;
                                            $value = $this->mdl_vendor_custom->form_value('cf_' . $custom_field->custom_field_id);
                                            ?>
                                            <th><?php _htmlsc($column); ?></th>
                                            <td><?php _htmlsc($value); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <?php
            if ($custom_fields) : ?>
                <hr>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-default no-margin">

                            <div class="panel-heading">
                                <?php _trans('custom_fields'); ?>
                            </div>
                            <div class="panel-body table-content">
                                <table class="table no-margin">
                                    <?php foreach ($custom_fields as $custom_field) : ?>
                                        <?php if ($custom_field->custom_field_location != 0) {
                                            continue;
                                        } ?>
                                        <tr>
                                            <?php
                                            $column = $custom_field->custom_field_label;
                                            $value = $this->mdl_vendor_custom->form_value('cf_' . $custom_field->custom_field_id);
                                            ?>
                                            <th><?php _htmlsc($column); ?></th>
                                            <td><?php _htmlsc($value); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                            <?php _trans('notes'); ?>
                        </div>
                        <div class="panel-body">
                            <div id="notes_list">
                                <?php echo $partial_notes; ?>
                            </div>
                            <input type="hidden" name="vendor_id" id="vendor_id"
                                   value="<?php echo $vendor->vendor_id; ?>">
                            <div class="input-group">
                                <textarea id="vendor_note" class="form-control" rows="2" style="resize:none"></textarea>
                                <span id="save_vendor_note" class="input-group-addon btn btn-default">
                                    <?php _trans('add_note'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div id="vendorQuotes" class="tab-pane table-content">
            <?php echo $quote_table; ?>
        </div>

        <div id="vendorInvoices" class="tab-pane table-content">
            <?php echo $invoice_table; ?>
        </div>

        <div id="vendorPayments" class="tab-pane table-content">
            <?php echo $payment_table; ?>
        </div>
    </div>

</div>
