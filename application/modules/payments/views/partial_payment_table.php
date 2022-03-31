<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('payment_date'); ?></th>
            <th><?php _trans('invoice_date'); ?></th>
            <th><?php _trans('invoice'); ?></th>
            <?php
            if(!isset($jns))
            {
                echo '<th>Vendor Name/Client Name</th>';
                $bahasa=get_setting('default_language');
                echo '<th>'.($bahasa=='english' ? 'Payment Status' : ($bahasa=='indonesia' ? 'Jenis Payment' : '')).'</th>';
            }
            else
            {
                
                echo '<th>'.($jns=='in' ? 'Vendor Name':'Client Name').'</th>';
            }
            ?>
            <th><?php _trans('amount'); ?></th>
            <th><?php _trans('payment_method'); ?></th>
            <th><?php _trans('note'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php 
            $data=array();
            if(isset($jns))
            {
                if($jns=='in')
                    $st_inv='out';
                else
                    $st_inv='in';

                foreach ($payments as $payment) { ?>
                    <?php
                    // if($payment->status_payments==$jns)

                    $inv=$this->db->from('ip_invoices')->where('invoice_id',$payment->invoice_id)->where('status_invoices',$st_inv)->get()->row();

                    if(count($inv)!=0)
                    {    
                    ?>
                    <tr>
                        <td><?php echo date_from_mysql($payment->payment_date); ?></td>
                        <td><?php echo date_from_mysql($payment->invoice_date_created); ?></td>
                        <td><?php echo anchor('invoices/view/' . $payment->invoice_id, $payment->invoice_number); ?></td>
                        <?php
                        
                        if($inv->vendor_id!=0)
                        {
                            $vendor=$this->db->from('ip_vendors')->where('vendor_id',$inv->vendor_id)->get()->row();
                        ?>
                            <td>
                                <a href="<?php echo site_url('vendors/view/' . $vendor->vendor_id); ?>"
                                title="<?php _trans('view_vendor'); ?>">
                                    <?php _htmlsc(format_vendor($vendor)); ?>
                                </a>
                            </td>
                        <?php
                        }
                        else
                        {
                        ?>
                        <td>
                            <a href="<?php echo site_url('clients/view/' . $payment->client_id); ?>"
                            title="<?php _trans('view_client'); ?>">
                                <?php _htmlsc(format_client($payment)); ?>
                            </a>
                        </td>
                        <?php
                        }
                        ?>
                        <td><?php echo format_currency($payment->payment_amount); ?></td>
                        <td><?php _htmlsc($payment->payment_method_name); ?></td>
                        <td><?php _htmlsc($payment->payment_note); ?></td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                <?php
                                if($jns=='in')
                                {
                                ?>
                                    <!-- <li>
                                        <a href="<?php echo site_url('payments/printkwitansi/' . $payment->payment_id); ?>" target="_blank">
                                            <i class="fa fa-print fa-margin"></i> Print Kwitansi
                                        </a>
                                    </li>  -->
                                <?php
                                }
                                ?>
                                    <li>
                                        <a href="<?php echo site_url('payments/form/' . $payment->payment_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i>
                                            <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('payments/delete/' . $payment->payment_id); ?>"
                                        onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i>
                                            <?php _trans('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php 
                        $data=1;
                    } 
                    else
                    {
                        $data=0;
                        continue;
                    }
                }
            }
            else
            {
                foreach ($payments as $payment) { 
                $st_inv=$jns=$payment->status_payments;
                $inv=$this->db->from('ip_invoices')->where('invoice_id',$payment->invoice_id)->get()->row();

                if(count($inv)!=0)
                {    
                ?>
                <tr>
                    <td><?php echo date_from_mysql($payment->payment_date); ?></td>
                    <td><?php echo date_from_mysql($payment->invoice_date_created); ?></td>
                    <td><?php echo anchor('invoices/view/' . $payment->invoice_id, $payment->invoice_number); ?></td>
                    <?php
                    
                    if($inv->vendor_id!=0)
                    {
                        $vendor=$this->db->from('ip_vendors')->where('vendor_id',$inv->vendor_id)->get()->row();
                    ?>
                        <td>
                            <a href="<?php echo site_url('vendors/view/' . $vendor->vendor_id); ?>"
                            title="<?php _trans('view_vendor'); ?>">
                                <?php _htmlsc(format_vendor($vendor)); ?>
                            </a>
                        </td>
                    <?php
                    }
                    else
                    {
                    ?>
                    <td>
                        <a href="<?php echo site_url('clients/view/' . $payment->client_id); ?>"
                        title="<?php _trans('view_client'); ?>">
                            <?php _htmlsc(format_client($payment)); ?>
                        </a>
                    </td>
                    <?php
                    }
                    ?>
                    <td><?php echo 'Payment '.($payment->status_payments); ?></td>
                    <td><?php echo format_currency($payment->payment_amount); ?></td>
                    <td><?php _htmlsc($payment->payment_method_name); ?></td>
                    <td><?php _htmlsc($payment->payment_note); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                            <?php
                            if($jns=='in')
                            {
                            ?>
                                <!-- <li>
                                    <a href="<?php echo site_url('payments/printkwitansi/' . $payment->payment_id); ?>" target="_blank">
                                        <i class="fa fa-print fa-margin"></i> Print Kwitansi
                                    </a>
                                </li>  -->
                            <?php
                            }
                            ?>
                                <li>
                                    <a href="<?php echo site_url('payments/form/' . $payment->payment_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i>
                                        <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('payments/delete/' . $payment->payment_id); ?>"
                                    onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i>
                                        <?php _trans('delete'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php 
                    $data=1;
                } 
                else
                {
                    $data=0;
                    continue;
                }
            }
        }

        if($data==0)
        {
            echo '<tr>
                    <td colspan="8" class="text-center">&nbsp;</td>
            </tr>';
        }
        ?>

        </tbody>

    </table>
</div>
