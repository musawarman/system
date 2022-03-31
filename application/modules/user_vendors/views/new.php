<script>
    $(function () {
        $('#user_all_vendors').click(function () {
            all_vendor_check();
        });
        
        function all_vendor_check() {
            if ($('#user_all_vendors').is(':checked')) {
                $('#list_vendor').hide();
            } else {
                $('#list_vendor').show();
            }
        }
        
        all_vendor_check();
    });
</script>

<form method="post">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('assign_vendor'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <input type="hidden" name="user_id" id="user_id"
                       value="<?php echo $user->user_id ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _htmlsc($user->user_name) ?>
                    </div>
                    <div class="panel-body">
                    
                        <div class="alert alert-info">
                            <label>
                                <input type="checkbox" name="user_all_vendors" id="user_all_vendors" value="1" <?php echo ($user->user_all_vendors)?'checked="checked"':''; ?>> <?php _trans('user_all_vendors') ?>
                            </label>
                                
                            <div>
                                <?php _trans('user_all_vendors_text') ?>
                            </div>
                        </div>
                        
                        <div id="list_vendor">
                            <label for="vendor_id"><?php _trans('vendor'); ?></label>
                            <select name="vendor_id" id="vendor_id" class="form-control simple-select"
                                    autofocus="autofocus">
                                <?php
                                foreach ($vendors as $vendor) {
                                    echo '<option value="' . $vendor->vendor_id . '">';
                                    echo htmlsc(format_vendor($vendor)) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        

                    </div>
                </div>

            </div>
        </div>

    </div>

</form>