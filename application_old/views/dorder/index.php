<div id="headerbar">

    <h1 class="headerbar-title">Data DO (Delivery Order Order) <?=ucwords($st)?> - <?=ucwords($jns)?></h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php _trans('submenu'); ?>
        </button>
        <a class="btn btn-primary btn-sm" href="<?php echo site_url('dorder/form/'.$st.'-new'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        
    </div>

    <div class="headerbar-item pull-right visible-lg">
       
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            
        </div>

        <div class="submenu-row">
           
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice Number</th>
                    <th>DO Number</th>
                    <th>DO Date</th>
                    <?php
                    if($jns=='out')
                    {
                    ?>
                    <th>Client Name</th>
                    <?php
                    }
                    else
                    {
                    ?>
                    <th>Vendor Name</th>
                    <?php   
                    }
                    ?>
                    <th><?php _trans('options'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $no=1;
                foreach ($dorder as $po) : 
                    $invoice=$dinvoice[$po->invoice_id];
                    if(isset($quotes[$invoice->quote_id]))
                    {  
                        $quote=$quotes[$invoice->quote_id];

                        if($quote->vendor_id!=0)
                            $vendor_name=$vendors[$quote->vendor_id]->vendor_name;
                        else
                            $vendor_name=$clients[$quote->client_id]->client_name;

                    if(isset($dinvoice[$invoice->invoice_id]))
                        $po_id=$dinvoice[$invoice->invoice_id]->po_id;
                    else
                        $po_id=0;
                      
                ?>
                    <tr>
                        <td class="text-center"><?=$no?></td>
                        <td><?=$invoice->invoice_number?></td>
                        <td><?=$po->do_number?></td>
                        <td class="text-right"><?=$po->do_ship_date?></td>
                        <td class="text-center"><b><?=$vendor_name?></b></td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo site_url('po/printcetak/' . $po_id); ?>" target="_blank">
                                            <i class="fa fa-print"></i>&nbsp;&nbsp; Print PO
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('dorder/printcetak/' . $po->invoice_id); ?>" target="_blank">
                                            <i class="fa fa-print"></i>&nbsp;&nbsp; Print DO
                                        </a>
                                    </li>
                                    
                                    
                                    
                                    <li>
                                        <a href="<?php echo site_url('dorder/form'.$st.'/' .$po->invoice_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                   
                                    <li>
                                        <a href="<?php echo site_url('dorder/delete/' . $po->do_id); ?>"
                                        onclick="return confirm('<?php _trans('delete_po_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php 
                }
                $no++;
                endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>
