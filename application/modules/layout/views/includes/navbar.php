<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?php echo trans('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><?php echo anchor('dashboard', trans('dashboard'), 'class="hidden-md"') ?>
                    <?php echo anchor('dashboard', '<i class="fa fa-dashboard"></i>', 'class="visible-md-inline-block"') ?>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md">Data</span>
                        <i class="visible-md-inline fa fa-users"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Client</a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('clients/form', trans('add_client')); ?></li>
                                <li><?php echo anchor('clients/index', trans('view_clients')); ?></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Supplier</a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('supplier/form', 'Add Supplier'); ?></li>
                                <li><?php echo anchor('supplier/index', 'View Supplier'); ?></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Vendor</a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('vendors/form', trans('Add Vendor')); ?></li>
                                <li><?php echo anchor('vendors/index', trans('View Vendors')); ?></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('clients'); ?> / <?php _trans('Vendors'); ?></span>
                        <i class="visible-md-inline fa fa-users"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('clients/form', trans('add_client')); ?></li>
                        <li><?php echo anchor('clients/index', trans('view_clients')); ?></li>
                        <li><?php echo anchor('vendors/form', trans('Add Vendor')); ?></li>
                        <li><?php echo anchor('vendors/index', trans('View Vendors')); ?></li>
                    </ul>
                </li> -->
                

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('quotes'); ?></span>
                        <i class="visible-md-inline fa fa-file"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('create_quote'); ?></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="create-quote-in">Quotes IN</a></li>
                                <li><a href="#" class="create-quote-out">Quotes OUT</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('view_quotes'); ?></a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('quotes/index/in', 'Quotes IN'); ?></li>
                                <li><?php echo anchor('quotes/index/out', 'Quotes OUT'); ?></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md">PO</span>
                        <i class="visible-md-inline fa fa-file"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Create PO</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?=site_url('po/form/in-new')?>">PO IN</a></li>
                                <li><a href="<?=site_url('po/form/out-new')?>">PO OUT</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">View PO</a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('po/index/in-all', 'PO IN'); ?></li>
                                <li><?php echo anchor('po/index/out-all', 'PO OUT'); ?></li>
                            </ul>
                        </li>
                        
                        
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md">DO</span>
                        <i class="visible-md-inline fa fa-file"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Create DO</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?=site_url('dorder/form/in-new')?>">DO IN</a></li>
                                <li><a href="<?=site_url('dorder/form/out-new')?>">DO OUT</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">View DO</a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('dorder/index/in-all', 'DO IN'); ?></li>
                                <li><?php echo anchor('dorder/index/out-all', 'DO OUT'); ?></li>
                            </ul>
                        </li>
                        
                        
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('invoices'); ?></span>
                        <i class="visible-md-inline fa fa-file-text"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('create_invoice'); ?></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?=site_url('invoice/formnew/in')?>" class="">Invoice IN</a></li>
                                <li><a href="<?=site_url('invoice/formnew/out')?>" class="">Invoice OUT</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('view_invoices'); ?></a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('invoices/index/in-all', 'Invoice IN'); ?></li>
                                <li><?php echo anchor('invoices/index/out-all', 'Invoice OUT'); ?></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('view_recurring_invoices'); ?></a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('invoices/recurring/index', 'Invoice IN'); ?></li>
                                <li><?php echo anchor('invoices/recurring/index', 'Invoice OUT'); ?></li>
                            </ul>
                        </li>
                        <!-- <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">View <?php _trans('DO'); ?></a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('dorder/index/in-all', 'DO IN'); ?></li>
                                <li><?php echo anchor('dorder/index/out-all', 'DO OUT'); ?></li>
                            </ul>
                        </li> -->
                        <!-- <li><a href="#" class="create-invoice"><?php _trans('create_invoice'); ?></a></li> -->
                        <!-- <li><?php echo anchor('invoices/index', trans('view_invoices')); ?></li> -->
                        <!-- <li><?php echo anchor('invoices/recurring/index', trans('view_recurring_invoices')); ?></li> -->
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('payments'); ?></span>
                        <i class="visible-md-inline fa fa-credit-card"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('enter_payment'); ?></a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('payment/form/in', 'Payment IN'); ?></li>
                                <li><?php echo anchor('payment/form/out', 'Payment OUT'); ?></li>
                            </ul> -->
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _trans('view_payments'); ?></a>
                            <ul class="dropdown-menu">
                                <li><?php echo anchor('payments/index/in', 'Payment IN'); ?></li>
                                <li><?php echo anchor('payments/index/out', 'Payment OUT'); ?></li>
                            </ul>
                        </li>
                        <!-- <li><?php echo anchor('payments/form', trans('enter_payment')); ?></li>
                        <li><?php echo anchor('payments/index', trans('view_payments')); ?></li> -->
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('products'); ?></span>
                        <i class="visible-md-inline fa fa-database"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('products/form', trans('create_product')); ?></li>
                        <li><?php echo anchor('products/index', trans('view_products')); ?></li>
                        <li><?php echo anchor('families/index', trans('product_families')); ?></li>
                        <li><?php echo anchor('units/index', trans('product_units')); ?></li>
                    </ul>
                </li>

                <li class="dropdown <?php echo get_setting('projects_enabled') == 1 ?: 'hidden'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('tasks'); ?></span>
                        <i class="visible-md-inline fa fa-check-square-o"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('tasks/form', trans('create_task')); ?></li>
                        <li><?php echo anchor('tasks/index', trans('show_tasks')); ?></li>
                        <li><?php echo anchor('projects/index', trans('projects')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('reports'); ?></span>
                        <i class="visible-md-inline fa fa-bar-chart"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('reports/invoice_aging', trans('invoice_aging')); ?></li>
                        <li><?php echo anchor('reports/payment_history', trans('payment_history')); ?></li>
                        <li><?php echo anchor('reports/sales_by_client', trans('sales_by_client')); ?></li>
                        <li><?php echo anchor('reports/sales_by_year', trans('sales_by_date')); ?></li>
                        <li><a href="<?=site_url()?>/jurnal">Journal</a></li>
                        <li><a href="<?=site_url()?>/procal">Laporan Laba Rugi</a></li>
                        <li><a href="<?=site_url()?>/grafik">Grafik</a></li>
                    </ul>
                </li>

            </ul>

            <?php if (isset($filter_display) and $filter_display == true) { ?>
                <?php $this->layout->load_view('filter/jquery_filter'); ?>
                <form class="navbar-form navbar-left" role="search" onsubmit="return false;">
                    <div class="form-group">
                        <input id="filter" type="text" class="search-query form-control input-sm"
                               placeholder="<?php echo $filter_placeholder; ?>">
                    </div>
                </form>
            <?php } ?>

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="http://docs.mzi.co.id/" target="_blank"
                       class="tip icon" title="<?php _trans('documentation'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-question-circle"></i>
                        <span class="visible-xs">&nbsp;<?php _trans('documentation'); ?></span>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="tip icon dropdown-toggle" data-toggle="dropdown"
                       title="<?php _trans('settings'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-cogs"></i>
                        <span class="visible-xs">&nbsp;<?php _trans('settings'); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('custom_fields/index', trans('custom_fields')); ?></li>
                        <li><?php echo anchor('email_templates/index', trans('email_templates')); ?></li>
                        <li><?php echo anchor('invoice_groups/index', trans('invoice_groups')); ?></li>
                        <li><?php echo anchor('invoices/archive', trans('invoice_archive')); ?></li>
                        <!-- // temporarily disabled
                        <li><?php echo anchor('item_lookups/index', trans('item_lookups')); ?></li>
                        -->
                        <li><?php echo anchor('payment_methods/index', trans('payment_methods')); ?></li>
                        <li><?php echo anchor('tax_rates/index', trans('tax_rates')); ?></li>
                        <li><?php echo anchor('users/index', trans('user_accounts')); ?></li>
                        <li class="divider hidden-xs hidden-sm"></li>
                        <li><?php echo anchor('settings', trans('system_settings')); ?></li>
                        <li><?php echo anchor('import', trans('import_data')); ?></li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo site_url('users/form/' .
                        $this->session->userdata('user_id')); ?>"
                       class="tip icon" data-placement="bottom"
                       title="<?php
                       _htmlsc($this->session->userdata('user_name'));
                       if ($this->session->userdata('user_company')) {
                           print(" (" . htmlsc($this->session->userdata('user_company')) . ")");
                       }
                       ?>">
                        <i class="fa fa-user"></i>
                        <span class="visible-xs">&nbsp;<?php
                            _htmlsc($this->session->userdata('user_name'));
                            if ($this->session->userdata('user_company')) {
                                print(" (" . htmlsc($this->session->userdata('user_company')) . ")");
                            }
                            ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('sessions/logout'); ?>"
                       class="tip icon logout" data-placement="bottom"
                       title="<?php _trans('logout'); ?>">
                        <i class="fa fa-power-off"></i>
                        <span class="visible-xs">&nbsp;<?php _trans('logout'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<style>
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -6px;
    margin-left: -1px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover>.dropdown-menu {
    display: block;
}

.dropdown-submenu>a:after {
    display: block;
    content: " ";
    float: right;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-left-color: #ccc;
    margin-top: 5px;
    margin-right: -10px;
}

.dropdown-submenu:hover>a:after {
    border-left-color: #fff;
}

.dropdown-submenu.pull-left {
    float: none;
}

.dropdown-submenu.pull-left>.dropdown-menu {
    left: -100%;
    margin-left: 10px;
    -webkit-border-radius: 6px 0 6px 6px;
    -moz-border-radius: 6px 0 6px 6px;
    border-radius: 6px 0 6px 6px;
}
</style>