<script>
    $(function () {
        // Display the create quote modal
        $('#create-quote').modal('show');

        $('.simple-select').select2();

        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>

        // Toggle on/off permissive search on clients names
        $('span#toggle_permissive_search_clients').click(function () {
            if ($('input#input_permissive_search_clients').val() == ('1')) {
                $.get("<?php echo site_url('clients/ajax/save_preference_permissive_search_clients'); ?>", {
                    permissive_search_clients: '0'
                });
                $('input#input_permissive_search_clients').val('0');
                $('span#toggle_permissive_search_clients i').removeClass('fa-toggle-on');
                $('span#toggle_permissive_search_clients i').addClass('fa-toggle-off');
            } else {
                $.get("<?php echo site_url('clients/ajax/save_preference_permissive_search_clients'); ?>", {
                    permissive_search_clients: '1'
                });
                $('input#input_permissive_search_clients').val('1');
                $('span#toggle_permissive_search_clients i').removeClass('fa-toggle-off');
                $('span#toggle_permissive_search_clients i').addClass('fa-toggle-on');
            }
        });

        // Creates the quote
        $('#quote_create_confirm').click(function () {
            console.log('clicked');
            // Posts the data to validate and create the quote;
            // will create the new client if necessary
            $.post("<?php echo site_url('quotes/ajax/create'); ?>", {
                    client_id: $('#create_quote_client_id').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_password: $('#quote_password').val(),
                    vendor_id: $('#vendor_id').val(),
                    quote_number: $('#quote_number').val(),
                    status_quotes: $('#status_quotes').val(),
                    user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                    invoice_group_id: $('#invoice_group_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful and quote was created
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + response.quote_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });
</script>
<?php 
$jns=$_POST['jns'];
?>
<div id="create-quote" class="modal modal-lg" role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('create_quote'); ?></h4>
        </div>
        <div class="modal-body">

            <input class="hidden" id="input_permissive_search_clients"
                   value="<?php echo get_setting('enable_permissive_search_clients'); ?>">

            <?php
            if($jns=='in')
            {
            ?>
            <div class="form-group">
                <label for="quote_password">Quotes Number</label>
                <input type="text" name="quote_number" id="quote_number" class="form-control"
                       value="<?php echo get_setting('quote_number') ? '' : get_setting('quote_number') ?>"
                       autocomplete="off">
            </div>
            <?php
            }
            else
            {
                echo '<input type="hidden" name="quote_number" id="quote_number" value="-">';       
            }
            ?>
            <div class="form-group has-feedback">
                <label for="create_quote_client_id">
                    <?php 
                        if($jns=='out')
                            echo 'Client Name'; 
                        else
                            echo 'Vendor Name'; 
                        
                    ?>
                </label>
                <div class="input-group">
                    <input type="hidden" name="vendor_id" id="vendor_id" value="0">
                    <?php
                    if($jns=='in')
                    {
                    ?>
                        <select name="client_id" id="create_quote_client_id" class="vendor-id-select form-control"
                                autofocus="autofocus" placeholder="Vendor Name" data-placeholder="Vendor Name">
                            <?php if (!empty($vendor)) : ?>
                                <option value="<?php echo $vendor->vendor_id; ?>"><?php _htmlsc(format_client($vendor)); ?></option>
                            <?php endif; ?>
                        </select>
                        <span id="toggle_permissive_search_clients" class="input-group-addon" title="<?php _trans('enable_permissive_search_clients'); ?>" style="cursor:pointer;">
                            <i class="fa fa-toggle-<?php echo get_setting('enable_permissive_search_clients') ? 'on' : 'off' ?> fa-fw" ></i>
                        </span>
                    <?php
                    }
                    else
                    {
                    ?>
                        <select name="client_id" id="create_quote_client_id" class="client-id-select form-control"
                                autofocus="autofocus">
                            <?php if (!empty($client)) : ?>
                                <option value="<?php echo $client->client_id; ?>"><?php _htmlsc(format_client($client)); ?></option>
                            <?php endif; ?>
                        </select>
                        <span id="toggle_permissive_search_clients" class="input-group-addon" title="<?php _trans('enable_permissive_search_clients'); ?>" style="cursor:pointer;">
                            <i class="fa fa-toggle-<?php echo get_setting('enable_permissive_search_clients') ? 'on' : 'off' ?> fa-fw" ></i>
                        </span>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="form-group has-feedback">
                <label for="create_quote_client_id">Quotes Category</label>
                <div class="input-group">
                    <select name="status_quotes"  id="status_quotes" <?=($jns=='0' ? '' :'disabled')?> class="form-control"
                            autofocus="autofocus" style="width:120px;">
                        <?php
                        if($jns=='0')
                        {
                        ?>
                            <option value="in">Quotes IN</option>
                            <option value="out">Quotes OUT</option>
                        <?php
                        }
                        else
                        {
                        ?>
                            <option value="in" <?=($jns=='in' ? 'selected="selected"' : '')?>>Quotes IN</option>
                            <option value="out" <?=($jns=='out' ? 'selected="selected"' : '')?>>Quotes OUT</option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="quote_date_created">
                    <?php _trans('quote_date'); ?>
                </label>

                <div class="input-group">
                    <input name="quote_date_created" id="quote_date_created"
                           class="form-control datepicker"
                           value="<?php echo date(date_format_setting()); ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="quote_password"><?php _trans('quote_password'); ?></label>
                <input type="text" name="quote_password" id="quote_password" class="form-control"
                       value="<?php echo get_setting('quote_pre_password') ? '' : get_setting('quote_pre_password') ?>"
                       autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id"><?php _trans('invoice_group'); ?>: </label>
                <select name="invoice_group_id" id="invoice_group_id" class="form-control simple-select">
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                            <?php check_select(get_setting('default_quote_group'), $invoice_group->invoice_group_id); ?>>
                            <?php _htmlsc($invoice_group->invoice_group_name); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="quote_create_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
