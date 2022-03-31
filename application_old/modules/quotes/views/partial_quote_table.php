<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('status'); ?></th>
            <th><?php _trans('quote'); ?></th>
            <th><?php _trans('created'); ?></th>
            <th><?php _trans('due_date'); ?></th>
            <th><?php _trans('client_name'); ?></th>
            <?php
            if(!isset($jns))
            {
                $bahasa=get_setting('default_language');
                echo '<th>'.($bahasa=='english' ? 'Quotes Status' : ($bahasa=='indonesia' ? 'Jenis Quotes' : '')).'</th>';
            }
            ?>
            <th style="text-align: right; padding-right: 25px;"><?php _trans('amount'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php



        $quote_idx = 1;
        $quote_count = count($quotes);
        $quote_list_split = $quote_count > 3 ? $quote_count / 2 : 9999;
        $data=0;
        foreach ($quotes as $quote) {
            if(isset($jns))
            {
                if($quote->status_quotes==$jns)
                {
                    // if(in_array($quote->quote_id,$po))
                    //     continue;
                // Convert the dropdown menu to a dropup if quote is after the invoice split
                    $dropup = $quote_idx > $quote_list_split ? true : false;
                    ?>
                    <tr>
                        <td>
                            <span class="label <?php echo $quote_statuses[$quote->quote_status_id]['class']; ?>">
                                <?php echo $quote_statuses[$quote->quote_status_id]['label']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>"
                            title="<?php _trans('edit'); ?>">
                                <?php echo($quote->quote_number ? $quote->quote_number : $quote->quote_id); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo date_from_mysql($quote->quote_date_created); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($quote->quote_date_expires); ?>
                        </td>
                        <td>
                            <?php
                            if($quote->vendor_id!=0)
                            {
                                $vendor=$this->db->from('ip_vendors')->where('vendor_id',$quote->vendor_id)->get()->row();
                            ?>
                                <a href="<?php echo site_url('vendors/view/' . $quote->vendor_id); ?>"
                                title="<?php _trans('view_vendor'); ?>">
                                    <?php _htmlsc(format_vendor($vendor)); ?>
                                </a>
                            <?php
                            }
                            else
                            {
                            ?>
                                <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>"
                                title="<?php _trans('view_client'); ?>">
                                    <?php _htmlsc(format_client($quote)); ?>
                                </a>
                            <?php
                            }
                            ?>
                            
                        </td>
                        <td style="text-align: right; padding-right: 25px;">
                            <?php echo format_currency($quote->quote_total); ?>
                        </td>
                        <td>
                            <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                                <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                                href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php
                                    if($quote->status_quotes=='out' && $quote->quote_status_id==4)
                                    {
                                    ?>
                                    <li>
                                        <a href="<?php echo site_url('quotes/generate_po/' . $quote->quote_id); ?>"
                                        target="_blank">
                                            <i class="fa fa-file fa-margin"></i> Create PO
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/generate_pdf/' . $quote->quote_id); ?>"
                                        target="_blank">
                                            <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                        </a>
                                    </li>
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                    <li>
                                        <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/generate_pdf/' . $quote->quote_id); ?>"
                                        target="_blank">
                                            <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                                            <i class="fa fa-send fa-margin"></i> <?php _trans('send_email'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/delete/' . $quote->quote_id); ?>"
                                        onclick="return confirm('<?php _trans('delete_quote_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </a>
                                    </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $quote_idx++;
                    $data=$quote_idx;
                } 
                else
                {
                    $data=0;
                    continue;
                }
            }
            else
            {
                $dropup = $quote_idx > $quote_list_split ? true : false;
                    ?>
                    <tr>
                        <td>
                            <span class="label <?php echo $quote_statuses[$quote->quote_status_id]['class']; ?>">
                                <?php echo $quote_statuses[$quote->quote_status_id]['label']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>"
                            title="<?php _trans('edit'); ?>">
                                <?php echo($quote->quote_number ? $quote->quote_number : $quote->quote_id); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo date_from_mysql($quote->quote_date_created); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($quote->quote_date_expires); ?>
                        </td>
                        <td>
                            <?php
                            if($quote->vendor_id!=0)
                            {
                                $vendor=$this->db->from('ip_vendors')->where('vendor_id',$quote->vendor_id)->get()->row();
                            ?>
                                <a href="<?php echo site_url('vendors/view/' . $quote->vendor_id); ?>"
                                title="<?php _trans('view_vendor'); ?>">
                                    <?php _htmlsc(format_vendor($vendor)); ?>
                                </a>
                            <?php
                            }
                            else
                            {
                            ?>
                                <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>"
                                title="<?php _trans('view_client'); ?>">
                                    <?php _htmlsc(format_client($quote)); ?>
                                </a>
                            <?php
                            }
                            ?>
                            
                        </td>
                        <td style="text-align: center; padding-right: 25px;">
                            <?php echo 'Quotes '.ucwords($quote->status_quotes); ?>
                        </td>
                        <td style="text-align: right; padding-right: 25px;">
                            <?php echo format_currency($quote->quote_total); ?>
                        </td>
                        <td>
                            <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                                <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                                href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php
                                    if($quote->status_quotes=='out' && $quote->quote_status_id==4)
                                    {
                                    ?>
                                    <li>
                                        <a href="<?php echo site_url('quotes/generate_po/' . $quote->quote_id); ?>"
                                        target="_blank">
                                            <i class="fa fa-file fa-margin"></i> Create PO
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/generate_pdf/' . $quote->quote_id); ?>"
                                        target="_blank">
                                            <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                        </a>
                                    </li>
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                    <li>
                                        <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/generate_pdf/' . $quote->quote_id); ?>"
                                        target="_blank">
                                            <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                                            <i class="fa fa-send fa-margin"></i> <?php _trans('send_email'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('quotes/delete/' . $quote->quote_id); ?>"
                                        onclick="return confirm('<?php _trans('delete_quote_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </a>
                                    </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $quote_idx++;
                    $data=$quote_idx;
                                    
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
