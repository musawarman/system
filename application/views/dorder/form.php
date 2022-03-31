<form method="post" action="<?=site_url('dorder/proses/'.$jns)?>" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
            <br>
            <div id="headerbar" style="padding:10px 0 !important;">
                <h1 class="headerbar-title" style="font-size:17px;">Form Delivery Order (DO) <?=ucwords($jns)?></h1>
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
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="create_invoice_client_id">PO Number</label>
                        <div class="input-group">
                            <select name="po_number" id="po-number" class="invoice-number form-control"
                                    autofocus="autofocus" data-placeholder="PO Number" require="required">
                                    <option value="">PO Number</option>
                                    <?php
                                    $quote_id=0;
                                    $quote_status='';
                                    foreach($det as $k)
                                    {
                                        if(in_array($k->po_id,$dos))
                                            continue;

                                    //     // if($inv->quote_id==0)
                                    //     //     continue;

                                        // $inv=$dinvoice[$k->invoice_id];
                                        
                                        $qt=$quotess[$k->quotes_id];
                                        if($qt->vendor_id!=0)
                                        {
                                            $name=$vendors[$qt->vendor_id]->vendor_name;
                                            $idc=$vendors[$qt->vendor_id]->vendor_id;
                                            echo '<option value="'.$k->po_id.'__'.$k->quotes_id.'__'.$name.'__'.$idc.'__'.$k->po_id.'">'.$k->po_number.' - '.$name.'</option>';
                                        }
                                        elseif($qt->client_id!=0)
                                        {
                                            $name=$clients[$qt->client_id]->client_name;
                                            $idc=$clients[$qt->client_id]->client_id;
                                            echo '<option value="'.$k->po_id.'__'.$k->quotes_id.'__'.$name.'__'.$idc.'__'.$k->po_id.'">'.$k->po_number.' - '.$name.'</option>';
                                        }
                                        $quote_id=$k->quotes_id;
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
                                value="" readonly>
                                <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                            <input type="hidden" name="client_id" value="" id="client_id">
                            <input type="hidden" name="invoice_id" value="" id="invoice_id">
                            <input class="hidden" id="payment_method_id" value="<?php echo get_setting('invoice_default_payment_method'); ?>">
                        </div>
                    </div>

                    <div class="form-group has-feedback">
                        <label for="invoice_date_created">DO Ship Date</label>

                        <div class="input-group">
                            <input name="invoice_date_created" id="invoice_date_created"
                                class="form-control datepicker"
                                value="<?php echo date(date_format_setting()); ?>">
                                <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="supplier_language">
                            Upload File DO
                        </label>
                        <input type="file" name="do_file" id="do_file" class="form-control" accept=".pdf,.png">
                        <div class="form-group" style="display:none">
                        <label for="invoice_password"><?php _trans('invoice_password'); ?></label>
                        <input type="hidden" name="invoice_password" id="invoice_password" class="form-control"
                            value="<?php echo get_setting('invoice_pre_password') == '' ? '' : get_setting('invoice_pre_password'); ?>"
                            style="margin: 0 auto;" autocomplete="off">
                    </div>
                    <div class="form-group" style="margin-top:20px;">
                        <label for="supplier_language">
                            DO Number
                        </label>
                        <input type="text" name="do_number" id="do_number" class="form-control" value="<?=($jns=='out'?$number_do:'')?>">
                        
                    </div>
                    <div class="form-group" style="display:none">
                        <label for="invoice_group_id"><?php _trans('invoice_group'); ?></label>
                        <input type="hidden" name="invoice_group_id" value="3">
                    </div>

                
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_language">
                            Buyer
                        </label>
                        <input type="text" name="do_buyer" id="do_buyer" class="form-control">
                        
                    </div>
                    <div class="form-group">
                        <label for="supplier_language">
                            Driver
                        </label>
                        <input type="text" name="do_fob" class="form-control">
                        
                    </div>
                    <div class="form-group">
                        <label for="supplier_language">
                            Delivery Term
                        </label>
                        <textarea name="do_delivery_term" id="do_delivery_term" class="form-control" rows="4"></textarea>
                        
                    </div>
                    <div class="form-group">
                        <label for="supplier_language">
                            Sales Person
                        </label>
                        <input type="text" name="do_sales_person" id="do_sales_person" class="form-control">
                        
                    </div>
                    <div class="form-group">
                        <label for="supplier_language">
                            Shipped Via
                        </label>
                        <input type="text" name="do_shipped_via" id="do_shipped_via" class="form-control">
                        
                    </div>
                </div>
            </div>
                
                <input type="hidden" name="status_quotes" value="<?=$quote_status?>">
                <input type="hidden" name="status_invoices" value="<?=$jns?>">
            </div>
        </div>
        <div class="col-md-2">&nbsp;</div>
    </div>
    
</div>
<div class="row">
        <div class="col-md-1">&nbsp;</div>
        <div class="col-md-10">
            <h3>Data Item</h3>
            <div id="data-item" style="padding:20px 0px;"></div>
        </div>
        <div class="col-md-1">&nbsp;</div>
    </div>
</form>
<script>
    $(function () {
        $('.invoice-number').select2();
        
        $('#po-number').on('change',function(){
            // alert($(this).val());
            var id=$(this).val().split('__');
            var quote_id=id[1];
            var po_id=id[0];
            var name=id[2];
            var idclient=id[3];
            var invoice_id=id[4];
            $('#invoice_id').val(invoice_id);
            $('#client_name').val(name);
            $('#client_id').val(idclient);
            $('#client_id').val(idclient);
            $('#data-item').load('<?=site_url()?>/quotes/data_item_do/'+quote_id+'/po');
            // $('#data-item').text($(this).val());

        });
    });

</script>