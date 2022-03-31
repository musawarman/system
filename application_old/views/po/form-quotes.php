
<script type="text/javascript">
    $(function () {
        $("#supplier_country").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<form method="post" action="<?=site_url('po/proses/-1')?>" enctype="multipart/form-data">

    
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

    <div id="headerbar">
        <h1 class="headerbar-title">Form Create PO <?=ucwords($jns)?></h1>
        <?php //$this->layout->load_view('layout/header_buttons'); ?>
        <div class="headerbar-item pull-right">
            <div class="btn-group btn-group-sm">
                <?php if (!isset($hide_submit_button)) : ?>
                    <button id="btn-submit" name="btn_submit" class="btn btn-success" value="1">
                        <i class="fa fa-check"></i> <?php _trans('save'); ?>
                    </button>
                <?php endif; ?>
                <?php if (!isset($hide_cancel_button)) : ?>
                    <button id="btn-cancel" name="btn_cancel" class="btn btn-danger" value="1">
                        <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden">
        <input class="hidden" name="q_st" type="hidden" value="<?=$po_st?>">
        

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        PO Information
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="supplier_name">
                                Quotes Number
                            </label>
                            <select name="quotes_id" id="quotes_id" class="form-control simple-select" data-placeholder="Choose Quotes Number" placeholder="Choose Quotes Number">
                                <!-- <option value="0">Choose Quotes Number</option> -->
                                <?php
                                foreach($quotes as $item)
                                {
                                    if($id!=-1)
                                    {
                                        if($det[0]->quotes_id==$item->quote_id)
                                        {
                                            if($item->vendor_id!=0)
                                            {
                                                if(isset($clients[$item->vendor_id]))
                                                {
                                                    $vendor=$vendors[$item->vendor_id];
                                                    echo '<option value="'.$item->quote_id.'" selected="selected">'.$item->quote_number.' - '.$vendor->vendor_name.'</option>';
                                                }
                                            }
                                            elseif($item->client_id!=0)
                                            {
                                                if(isset($clients[$item->client_id]))
                                                {
                                                    $client=$clients[$item->client_id];
                                                    echo '<option value="'.$item->quote_id.'" selected="selected">'.$item->quote_number.' - '.$client->client_name.'</option>';
                                                }
                                            }
                                            
                                        }
                                        else
                                        {
                                            if($item->vendor_id!=0)
                                            {
                                                if(isset($clients[$item->vendor_id]))
                                                {
                                                    $vendor=$vendors[$item->vendor_id];
                                                    echo '<option value="'.$item->quote_id.'">'.$item->quote_number.' - '.$vendor->vendor_name.'</option>';
                                                }
                                            }
                                            elseif($item->client_id!=0)
                                            {
                                                if(isset($clients[$item->client_id]))
                                                {
                                                    $client=$clients[$item->client_id];
                                                    echo '<option value="'.$item->quote_id.'">'.$item->quote_number.' - '.$client->client_name.'</option>';
                                                }
                                            }
                                            
                                        }
                                    }
                                    else
                                    {
                                        if(in_array($item->quote_id,$d_po))
                                            continue;

                                        if($item->vendor_id!=0)
                                        {
                                            if(isset($clients[$item->vendor_id]))
                                            {
                                                $vendor=$vendors[$item->vendor_id];
                                                echo '<option value="'.$item->quote_id.'">'.$item->quote_number.' - '.$vendor->vendor_name.'</option>';
                                            }
                                        }
                                        elseif($item->client_id!=0)
                                        {
                                            if(isset($clients[$item->client_id]))
                                            {
                                                $client=$clients[$item->client_id];
                                                echo '<option value="'.$item->quote_id.'">'.$item->quote_number.' - '.$client->client_name.'</option>';
                                            }
                                        }
                                        
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="supplier_birthdate">PO Date</label>
                            <?php
                            // $bdate = $this->mdl_clients->form_value('supplier_birthdate');
                            // } else {
                                // }
                                $bdate = date('d-m-Y');
                            ?>
                            <div class="input-group">
                                <input type="text" name="po_date" id="po_date"
                                       class="form-control"
                                       value="<?php _htmlsc($bdate); ?>">
                                <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        PO Number
                                    </label>
                                    <input type="text" name="po_number" id="po_number" class="form-control" value="<?=($jns=='in' ? $ponumber : '')?>">
                                </div>
                            </div>
                            <?php
                            if($jns=='in')
                            {
                            ?>
                            <div class="col-md-6">
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Customer PO Number
                                    </label>
                                    <input type="text" name="customer_number" id="customer_number" class="form-control" value="">
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="supplier_language">
                                Upload File PO
                            </label>
                            <input type="file" name="po_file" id="po_file" class="form-control" accept=".pdf,.png">
                            
                        </div>
                        

                    </div>
                </div>

            </div>
             <div class="col-xs-12 col-sm-6">
                <?php
                if($jns=='out')
                {
                ?>
                <div class="panel panel-default">

                    <div class="panel-heading">
                        Other Information
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_web">Supplier</label>

                                    <div class="controls">
                                        <select name="supplier_id" required="required" class="form-control simple-select" data-placeholder="Choose Supplier" placeholder="Choose Supplier">
                                            <option value="">Choose Supplier</option>
                                            <?php
                                            foreach($suppliers as $item)
                                            {
                                                echo '<option value="'.$item->supplier_id.'">'.$item->supplier_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Currency
                                    </label>
                                    <select name="currency"
                                        id="currency"
                                        class="input-sm form-control simple-select">
                                    <?php foreach ($gateway_currency_codes as $val => $key) { 
                                        ?>
                                        <option value="<?php echo $val; ?>"
                                            <?php check_select(get_setting('currency_code', '', true), $val); ?>>
                                            <?php echo $val; ?>
                                        </option>
                                    <?php 
                                        }
                                        ?>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Bank Name
                                    </label>
                                    <input type="text" name="po_bank_name" id="po_bank_name" class="form-control" value="">
                                </div>
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Payment Term
                                    </label>
                                    <input type="text" name="po_payment_term" id="po_payment_term" class="form-control" value="">
                                </div>
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Freight Terms
                                    </label>
                                    <input type="text" name="po_freight_term" id="po_freight_term" class="form-control" value="">
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Account Name
                                    </label>
                                    <input type="text" name="po_account_number" id="po_account_number" class="form-control" value="">
                                </div>
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Price Basic
                                    </label>
                                    <input type="text" name="po_price_basic" id="po_price_basic" class="form-control" value="">
                                </div>
                                <div class="form-group no-margin">
                                    <label for="supplier_language">
                                        Address All Queries To
                                    </label>
                                    <input type="text" name="po_address_all_queries" id="po_address_all_queries" class="form-control" value="">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <?php
                }
                ?>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        Items Information
                    </div>

                    <div class="panel-body">
                        <div id="data-item">
                        <?php
                        if($id!=-1)
                        {
                            $this->load->model('quotes/mdl_quotes');
                            $this->load->model('quotes/mdl_quote_items');
                            $this->load->model('tax_rates/mdl_tax_rates');
                            $this->load->model('quotes/mdl_quote_tax_rates');
                            $quote = $this->mdl_quotes->get_by_id($d[0]->quote_id);
                            $data['quote']=$quote;
                            $data['quote_id']=$d[0]->quote_id;
                            $data['items']=$this->mdl_quote_items->where('quote_id', $d[0]->quote_id)->get()->result();
                            $data['quote_tax_rates'] = $this->mdl_quote_tax_rates->where('quote_id', $d[0]->quote_id)->get()->result();
                            $this->load->view('quotes/data_item',$data);
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>

    </div>
</form>
<script>
$('#po_date').datepicker({ 
    format: 'dd-mm-yyyy' 
});
$('#quotes_id').on('change',function(){
    var quote_id=$(this).val();
    // alert(quote_id);
    $('#data-item').load('<?=site_url()?>/quotes/data_item/'+quote_id+'/po');
});
</script>