<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('status'); ?></th>
            <th><?php _trans('invoice'); ?></th>
            <th><?php _trans('created'); ?></th>
            <th><?php _trans('due_date'); ?></th>
            
            <?php
            if(!isset($jns))
            {
                echo '<th>Vendor Name/Client Name</th>';
                $bahasa=get_setting('default_language');
                echo '<th>'.($bahasa=='english' ? 'Invoices Status' : ($bahasa=='indonesia' ? 'Jenis Invoices' : '')).'</th>';
            }
            else
            {
                
                echo '<th>'.($jns=='in' ? 'Vendor Name':'Client Name').'</th>';
            }
            ?>
            <th style="text-align: center;"><?php _trans('amount'); ?></th>
            <th style="text-align: right;"><?php _trans('balance'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        
        $invoice_idx = 1;
        $invoice_count = count($invoices);
        $invoice_list_split = $invoice_count > 3 ? $invoice_count / 2 : 9999;
        $data=0;
        foreach ($invoices as $invoice) {
            $do=$this->db->query('select * from ip_do where invoice_id="'.$invoice->invoice_id.'"')->result();
            
            if($invoice->vendor_id!=0)
            {
                $vendor=$this->db->from('ip_vendors')->where('vendor_id',$invoice->vendor_id)->get()->row();
            }
            else
                $vendor=array();
            
            if(isset($jns))
            {
                if($invoice->status_invoices==$jns)
                {
                    if($invoice->quote_id==0)
                        continue;
                    // Disable read-only if not applicable
                    if ($this->config->item('disable_read_only') == true) {
                        $invoice->is_read_only = 0;
                    }
                    // Convert the dropdown menu to a dropup if invoice is after the invoice split
                    $dropup = $invoice_idx > $invoice_list_split ? true : false;
                    ?>
                    <tr>
                        <td>
                            <span class="label <?php echo $invoice_statuses[$invoice->invoice_status_id]['class']; ?>">
                                <?php echo $invoice_statuses[$invoice->invoice_status_id]['label'];
                                if ($invoice->invoice_sign == '-1') { ?>
                                    &nbsp;<i class="fa fa-credit-invoice"
                                            title="<?php echo trans('credit_invoice') ?>"></i>
                                <?php }
                                if ($invoice->is_read_only == 1) { ?>
                                    &nbsp;<i class="fa fa-read-only"
                                            title="<?php echo trans('read_only') ?>"></i>
                                <?php }; ?>
                            </span>
                        </td>

                        <td>
                            <a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>"
                            title="<?php _trans('edit'); ?>">
                                <?php echo($invoice->invoice_number ? $invoice->invoice_number : $invoice->invoice_id); ?>
                            </a>
                        </td>

                        <td>
                            <?php echo date_from_mysql($invoice->invoice_date_created); ?>
                        </td>

                        <td>
                            <span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>">
                                <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            if(count($vendor)!=0)
                            {
                            ?>
                                <a href="<?php echo site_url('vendors/view/' . $invoice->vendor_id); ?>"
                                title="<?php _trans('view_vendor'); ?>">
                                    <?php _htmlsc(format_vendor($vendor)); ?>
                                </a>
                            <?php
                            }
                            else
                            {
                            ?>
                                <a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>"
                                title="<?php _trans('view_client'); ?>">
                                    <?php _htmlsc(format_client($invoice)); ?>
                                </a>
                            <?php
                            }
                            ?>
                            
                        </td>

                        <td class="amount <?php if ($invoice->invoice_sign == '-1') {
                            echo 'text-danger';
                        }; ?>">
                            <?php echo format_currency($invoice->invoice_total); ?>
                        </td>

                        <td class="amount">
                            <?php echo format_currency($invoice->invoice_balance); ?>
                        </td>

                        <td>
                            <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if ($invoice->is_read_only != 1) { ?>
                                        <li>
                                            <a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>">
                                                <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <li>
                                        <a href="<?php echo site_url('dorder/printkwitansi/' . $invoice->invoice_id); ?>" target="_blank">
                                            <i class="fa fa-print fa-margin"></i> Print Kwitansi
                                        </a>
                                    </li>  
                                    <?php if ($invoice->status_invoices == 'in') { ?>
                                        <li>
                                                <?php if(count($do)!=0) {?>
                                                    <a href="<?php echo site_url('dorder/printcetak/' . $invoice->invoice_id); ?>"target="_blank">
                                                        <i class="fa fa-print fa-margin"></i> Download DO
                                                    </a>
                                                <?php } else {
                                                ?>
                                                    <a href="<?php echo site_url('dorder/form'.$jns.'/' . $invoice->invoice_id); ?>" target="_blank">
                                                        <i class="fa fa-file fa-margin"></i> Create DO
                                                    </a>
                                                <?php
                                                }?>
                                        </li>
                                    <?php } else {?>
                                            <?php if(count($do)!=0) {?>
                                                <li>
                                                    <a href="<?php echo site_url('dorder/generate_pdf/' . $invoice->invoice_id); ?>"target="_blank">
                                                        <i class="fa fa-print fa-margin"></i> Download DO
                                                    </a>
                                                </li> 
                                                
                                                <?php } else {
                                                ?>
                                                <li>
                                                    <a href="<?php echo site_url('dorder/form'.$jns.'/' . $invoice->invoice_id); ?>" target="_blank">
                                                        <i class="fa fa-file fa-margin"></i> Create DO
                                                    </a>
                                                </li>
                                                <?php
                                                }?>
                                    <?php } ?>
                                    
                                    <li>
                                            <a href="<?php echo site_url('invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                                            target="_blank">
                                                <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                            </a>
                                        </li>
                                    <li>
                                        <a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                                            <i class="fa fa-send fa-margin"></i> <?php _trans('send_email'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="invoice-add-payment"
                                        data-invoice-id="<?php echo $invoice->invoice_id; ?>"
                                        data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
                                        data-invoice-payment-method="<?php echo $invoice->payment_method; ?>">
                                            <i class="fa fa-money fa-margin"></i>
                                            <?php _trans('enter_payment'); ?>
                                        </a>
                                    </li>
                                    <?php if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)) { ?>
                                        <li>
                                            <a href="<?php echo site_url('invoices/delete/' . $invoice->invoice_id); ?>"
                                            onclick="return confirm('<?php _trans('delete_invoice_warning'); ?>');">
                                                <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $invoice_idx++;
                    $data=$invoice_idx;
                }
                else
                {
                    $data=0;
                    continue;
                }
            }
            else
            {
                    if($invoice->quote_id==0)
                        continue;
                    // Disable read-only if not applicable
                    if ($this->config->item('disable_read_only') == true) {
                        $invoice->is_read_only = 0;
                    }
                    // Convert the dropdown menu to a dropup if invoice is after the invoice split
                    $dropup = $invoice_idx > $invoice_list_split ? true : false;
                    ?>
                    <tr>
                        <td>
                            <span class="label <?php echo $invoice_statuses[$invoice->invoice_status_id]['class']; ?>">
                                <?php echo $invoice_statuses[$invoice->invoice_status_id]['label'];
                                if ($invoice->invoice_sign == '-1') { ?>
                                    &nbsp;<i class="fa fa-credit-invoice"
                                            title="<?php echo trans('credit_invoice') ?>"></i>
                                <?php }
                                if ($invoice->is_read_only == 1) { ?>
                                    &nbsp;<i class="fa fa-read-only"
                                            title="<?php echo trans('read_only') ?>"></i>
                                <?php }; ?>
                            </span>
                        </td>

                        <td>
                            <a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>"
                            title="<?php _trans('edit'); ?>">
                                <?php echo($invoice->invoice_number ? $invoice->invoice_number : $invoice->invoice_id); ?>
                            </a>
                        </td>

                        <td>
                            <?php echo date_from_mysql($invoice->invoice_date_created); ?>
                        </td>

                        <td>
                            <span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>">
                                <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            if(count($vendor)!=0)
                            {
                            ?>
                                <a href="<?php echo site_url('vendors/view/' . $invoice->vendor_id); ?>"
                                title="<?php _trans('view_vendor'); ?>">
                                    <?php _htmlsc(format_vendor($vendor)); ?>
                                </a>
                            <?php
                            }
                            else
                            {
                            ?>
                                <a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>"
                                title="<?php _trans('view_client'); ?>">
                                    <?php _htmlsc(format_client($invoice)); ?>
                                </a>
                            <?php
                            }
                            ?>
                            
                        </td>

                        <td class="amount <?php if ($invoice->invoice_sign == '-1') {
                            echo 'text-danger';
                        }; ?>">
                            <?php echo format_currency($invoice->invoice_total); ?>
                        </td>

                        <td class="text-center">
                            <?php echo 'Invoice '.ucwords($invoice->status_invoices); ?>
                        </td>
                        <td class="amount">
                            <?php echo format_currency($invoice->invoice_balance); ?>
                        </td>

                        <td>
                            <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if ($invoice->is_read_only != 1) { ?>
                                        <li>
                                            <a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>">
                                                <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <li>
                                        <a href="<?php echo site_url('dorder/printkwitansi/' . $invoice->invoice_id); ?>" target="_blank">
                                            <i class="fa fa-print fa-margin"></i> Print Kwitansi
                                        </a>
                                    </li>  
                                    
                                            <?php if(count($do)!=0) {?>
                                                <li>
                                                    <a href="<?php echo site_url('dorder/generate_pdf/' . $invoice->invoice_id); ?>"target="_blank">
                                                        <i class="fa fa-print fa-margin"></i> Download DO
                                                    </a>
                                                </li> 
                                                
                                                <?php } else {
                                                ?>
                                                <li>
                                                    <a href="<?php echo site_url('dorder/form'.$invoice->status_invoices.'/' . $invoice->invoice_id); ?>" target="_blank">
                                                        <i class="fa fa-file fa-margin"></i> Create DO
                                                    </a>
                                                </li>
                                                <?php
                                                }?>
                                   
                                    
                                    <li>
                                            <a href="<?php echo site_url('invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                                            target="_blank">
                                                <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                            </a>
                                        </li>
                                    <li>
                                        <a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                                            <i class="fa fa-send fa-margin"></i> <?php _trans('send_email'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="invoice-add-payment"
                                        data-invoice-id="<?php echo $invoice->invoice_id; ?>"
                                        data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
                                        data-invoice-payment-method="<?php echo $invoice->payment_method; ?>">
                                            <i class="fa fa-money fa-margin"></i>
                                            <?php _trans('enter_payment'); ?>
                                        </a>
                                    </li>
                                    <?php if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)) { ?>
                                        <li>
                                            <a href="<?php echo site_url('invoices/delete/' . $invoice->invoice_id); ?>"
                                            onclick="return confirm('<?php _trans('delete_invoice_warning'); ?>');">
                                                <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $invoice_idx++;
                    $data=$invoice_idx;
            }

        } 
        if($data==0)
        {
            echo '<tr>
                    <td colspan="8" class="text-center">Data Not Avaliable Yet</td>
            </tr>';
        }
        ?>
        </tbody>

    </table>
</div>
