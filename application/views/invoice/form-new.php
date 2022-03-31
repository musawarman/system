<form method="post" action="<?=site_url('invoice/proses/-1')?>" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
            <br>
            <div id="headerbar" style="padding:10px 0 !important;">
                <h1 class="headerbar-title" style="font-size:17px;">Form Create Invoice <?=ucwords($jns)?></h1>
                <?php //$this->layout->load_view('layout/header_buttons'); ?>
                <div class="headerbar-item pull-right">
                    <div class="btn-group btn-group-sm">
                        <?php if (!isset($hide_submit_button)) : ?>
                            <button id="btn-submit" name="btn_submit" class="btn btn-success" value="1">
                                <i class="fa fa-check"></i> <?php _trans('save'); ?>
                            </button>
                        <?php endif; ?>
                        <?php if (!isset($hide_cancel_button)) : ?>
                            <!-- <button id="btn-cancel" name="btn_cancel" class="btn btn-danger" value="1">
                                <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                            </button> -->
                             <a href="javascript:history.go(-1)" class="btn btn-danger"><i class="fa fa-times"></i> <?php _trans('cancel'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <br>
            <input class="hidden" id="payment_method_id"
                    value="<?php echo get_setting('invoice_default_payment_method'); ?>">

                <input class="hidden" id="input_permissive_search_clients"
                    value="<?php echo get_setting('enable_permissive_search_clients'); ?>">

                <div class="form-group has-feedback">
                    <label for="create_invoice_client_id">PO Number</label>
                    <div class="input-group">
                        <select name="po_number" id="po-number" class="po-number form-control"
                                autofocus="autofocus" data-placeholder="PO Number" require="required">
                                <option value="">PO Number</option>
                                <?php
                                $quote_id=0;
                                $quote_status='';
                                foreach($det as $k)
                                {
                                    if(in_array($k->po_id,$paid))
                                        continue;

                                    if(!in_array($k->po_id,$do))
                                        continue;

                                    if(isset($quotes[$k->quotes_id]))
                                    {
                                        $quote=$quotes[$k->quotes_id];
                                        $quote_id=$k->quotes_id;
                                        $quote_status=$quote->status_quotes;
                                        if($quote->vendor_id!=0)
                                        {
                                            $name=$vendors[$quote->vendor_id]->vendor_name;
                                            $idc=$vendors[$quote->vendor_id]->vendor_id;
                                            echo '<option value="'.$k->po_id.'__'.$k->quotes_id.'__'.$name.'__'.$idc.'">'.$k->po_number.' - '.$name.'</option>';
                                        }
                                        elseif($quote->client_id!=0)
                                        {
                                            $name=$clients[$quote->client_id]->client_name;
                                            $idc=$clients[$quote->client_id]->client_id;
                                            echo '<option value="'.$k->po_id.'__'.$k->quotes_id.'__'.$name.'__'.$idc.'">'.$k->po_number.' - '.$name.'</option>';
                                        }

                                    }
                                    // else
                                    //     echo '<option>'.$k->quotes_id.'</option>';
                                }
                                ?>
                        </select>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label for="create_invoice_client_id"><?php _trans('client'); ?></label>
                    <div class="input-group">
                        <input name="client_name" id="client_name"
                            class="form-control"
                            readonly>
                            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                        <input type="hidden" name="client_id" id="client_id">
                        <input class="hidden" id="payment_method_id" value="<?php echo get_setting('invoice_default_payment_method'); ?>">
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label for="invoice_date_created"><?php _trans('invoice_date'); ?></label>

                    <div class="input-group">
                        <input name="invoice_date_created" id="invoice_date_created"
                            class="form-control datepicker"
                            value="<?php echo date(date_format_setting()); ?>">
                            <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    </div>
                </div>
                <?php
                if($jns=='in')
                {
                ?>
                <div class="form-group">
                    <label for="supplier_language">
                        Upload File DO
                    </label>
                    <input type="file" name="do_file" id="do_file" class="form-control" accept=".pdf,.png">
                    
                </div>
                <div class="form-group">
                    <label for="supplier_language">
                        DO Number
                    </label>
                    <input type="text" name="do_number" id="do_number" class="form-control">
                    
                </div>
                <?php
                }
                else
                {
                    echo '<input type="hidden" name="do_number" id="do_number" class="form-control">';
                }
                ?>
                <div class="form-group" style="display:none">
                    <label for="invoice_password"><?php _trans('invoice_password'); ?></label>
                    <input type="hidden" name="invoice_password" id="invoice_password" class="form-control"
                        value="<?php echo get_setting('invoice_pre_password') == '' ? '' : get_setting('invoice_pre_password'); ?>"
                        style="margin: 0 auto;" autocomplete="off">
                </div>

                <div class="form-group" style="display:none">
                    <label for="invoice_group_id"><?php _trans('invoice_group'); ?></label>
                    <input type="hidden" name="invoice_group_id" value="3">
                </div>
                <input type="hidden" name="status_quotes" value="<?=$jns?>">
                <input type="hidden" name="status_invoices" value="<?=$jns?>">
        </div>
        <div class="col-md-2">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="data-item" style="padding:20px;"></div>
        </div>
    </div>
</div>
</form>
<script>
    $(function () {
        $('.po-number').select2();
        $('#po-number').on('change',function(){
            // alert($(this).val());
            var id=$(this).val().split('__');
            var quote_id=id[1];
            var po_id=id[0];
            var name=id[2];
            var idclient=id[3];
            $('#client_name').val(name);
            $('#client_id').val(idclient);
            $('#client_id').val(idclient);
            $('#data-item').load('<?=site_url()?>/quotes/data_item/'+quote_id+'/po');
        });
    });

</script>