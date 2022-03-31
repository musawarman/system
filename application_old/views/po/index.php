<div id="headerbar">

    <h1 class="headerbar-title">Data PO (Purchase Order) <?=ucwords($st)?> - <?=ucwords($jns)?></h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php _trans('submenu'); ?>
        </button>
        <a class="btn btn-primary btn-sm" href="<?php echo site_url('po/form/'.$st.'-new'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('po/status/'.$st.'-all'); ?>"
               class="btn  <?php echo $this->uri->segment(3) == $st.'-all' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('all'); ?>
            </a>
            <a href="<?php echo site_url('po/status/'.$st.'-process'); ?>"
               class="btn <?php echo $this->uri->segment(3) == $st.'-process' || !$this->uri->segment(3) ? 'btn-primary' : 'btn-default' ?>">
                Process Invoice
            </a>
            <a href="<?php echo site_url('po/status/'.$st.'-draft'); ?>"
               class="btn  <?php echo $this->uri->segment(3) == $st.'-draft' ? 'btn-primary' : 'btn-default' ?>">
                Draft
            </a>
            
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            
        </div>

        <div class="submenu-row">
            <div class="btn-group btn-group-sm index-options">
                <a href="<?php echo site_url('po/status/'.$st.'-process'); ?>"
                   class="btn <?php echo $this->uri->segment(3) == 'process' || !$this->uri->segment(3) ? 'btn-primary' : 'btn-default' ?>">
                    Process Invoice
                </a>
                <a href="<?php echo site_url('po/status/'.$st.'-draft'); ?>"
                   class="btn  <?php echo $this->uri->segment(3) == 'draft' ? 'btn-primary' : 'btn-default' ?>">
                    Draft
                </a>
                <a href="<?php echo site_url('po/status/'.$st.'-all'); ?>"
                   class="btn  <?php echo $this->uri->segment(3) == 'all' ? 'btn-primary' : 'btn-default' ?>">
                    <?php _trans('all'); ?>
                </a>
            </div>
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
                    <th>Status</th>
                    <th>PO Date</th>
                    <th>Client/Vendor Name</th>
                    <th>#</th>
                    <th>Sub Total Amount</th>
                    <th>Tax</th>
                    <th>Total Amount</th>
                    <?php
                    if($jns=='out')
                    {
                    ?>
                    <th>Supplier Name</th>
                    <?php
                    }
                    ?>
                    <th>File</th>
                    <th><?php _trans('options'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data_po as $po) : 
                    
                    if(isset($quotes[$po->quotes_id]))
                    {  
                        $quote=$quotes[$po->quotes_id];

                        
                        if($quote->vendor_id!=0)
                            $vendor_name=$vendors[$quote->vendor_id]->vendor_name;
                        else
                            $vendor_name=$clients[$quote->client_id]->client_name;


                        if(isset($suppliers[$po->supplier_id]))
                            $supplier_name=$suppliers[$po->supplier_id]->supplier_name;
                        else
                            $supplier_name='';

                        $totalamount=0;
                        $tax=0;
                        $totalamounttax=0;
                        $persen=0;
                        foreach($amounts[$po->quotes_id] as $item)
                        {
                            $totalamount+=$item->quote_item_subtotal;
                            $tax+=$item->quote_tax_total;
                            $totalamounttax+=$item->quote_total;

                            $persen=$item->quote_tax_total/$item->quote_item_subtotal * 100;
                        }
                ?>
                    <tr>
                        <td>
                        <?php
                        if($po->po_status==0)
                            echo '<span class="label label-default">Draft</span>';
                        elseif($po->po_status==1)
                            echo '<span class="label label-info">Process Invoice</span>';
                        ?>
                        </td>
                        <td><b><?php echo date('d/m/Y',strtotime($po->po_date)); ?></b></td>
                        <td><b><?php echo $vendor_name; ?></b></td>
                        <!-- <td><b><a href="<?=site_url('po/detail/'.$po->po_id)?>"><?php echo ($po->po_number); ?></a></b></td> -->
                        <td>
                            PO Number :<br><b><a href="#"><?php echo ($po->po_number); ?></a></b><br>
                            Quote Number : <br><b><a href="#"><?php echo ($quotes[$po->quotes_id]->quote_number); ?></a></b>
                        </td>
                        <td class="text-right">Rp. <?=format_currency_indo($totalamount)?></td>
                        <td class="text-center"><?=$persen?>%</td>
                        <td class="text-right">Rp. <?=format_currency_indo($totalamounttax)?></td>

                        <?php
                        if($jns=='out')
                        {
                        ?>
                        <td class="text-left"><?=$supplier_name?></td>
                        <?php
                        }
                        ?>
                        <td class="text-left">
                        <?php
                            if($po->file!='-' && $po->file!=null)
                                echo '<a href="'.base_url().'uploads/import/'.$po->file.'" target="_blank"><i class="fa fa-file-o"></i> Open File</a>';

                        ?>
                        </td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo site_url('po/printcetak/' . $po->po_id); ?>" target="_blank">
                                            <i class="fa fa-print"></i>&nbsp; Print PO
                                        </a>
                                    </li>
                                    
                                    <?php
                                    if($po->po_status==0){
                                    ?>
                                        <li>
                                            <a href="<?php echo site_url('invoice/form/' . $po->po_id); ?>">
                                                <i class="fa fa-file-pdf-o"></i>&nbsp; Create Invoice
                                            </a>
                                        </li>
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <li>
                                            <a href="<?php echo site_url('invoice/view/' . $po->po_id); ?>">
                                                <i class="fa fa-file"></i>&nbsp; Show Invoice
                                            </a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                    <li>
                                        <a href="<?php echo site_url('po/form/' . $st.'-'.$po->po_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                   
                                    <li>
                                        <a href="<?php echo site_url('po/delete/' . $po->po_id); ?>"
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
                endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>
