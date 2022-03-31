<?php

function load_config()
{
    $ci =& get_instance();
	$ci->load->dbforge();

	// if(!$ci->db->table_exists('ip_vendor_custom'))
	// {
	// 	$sql="CREATE TABLE `ip_vendor_custom` (
	// 	`client_custom_id` int(11) NOT NULL,
	// 	`client_id` int(11) NOT NULL,
	// 	`client_custom_fieldid` int(11) NOT NULL,
	// 	`client_custom_fieldvalue` text,
	// 	`vendor_custom_id` int(11) DEFAULT '0',
	// 	`vendor_id` int(11) DEFAULT '0',
	// 	`vendor_custom_fieldid` int(11) DEFAULT '0',
	// 	`vendor_custom_fieldvalue` int(11) DEFAULT '0'
	// 	) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

	// 	$ci->db->query($sql);
	// }

    if (!$ci->db->field_exists('vendor_id', 'ip_invoices'))
	{
	    $fields = array(
		        'vendor_id' => array(
					'type' => 'INTEGER',
					'default'=>0
	            )
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
	}
    if (!$ci->db->field_exists('no_kwitansi', 'ip_invoices'))
	{
	    $fields = array(
		        'no_kwitansi' => array(
					'type' => 'VARCHAR(255)'
	            )
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
	}
    
    if (!$ci->db->field_exists('user_all_vendors', 'ip_users'))
	{
	    $fields = array(
		        'user_all_vendors' => array(
					'type' => 'INTEGER',
					'default'=>0
	            )
		);
		$ci->dbforge->add_column('ip_users', $fields);
	}
    if (!$ci->db->field_exists('vendor_id', 'ip_quotes'))
	{
	    $fields = array(
		        'vendor_id' => array(
					'type' => 'INTEGER',
					'default'=>0
	            )
		);
		$ci->dbforge->add_column('ip_quotes', $fields);
	}
    if (!$ci->db->field_exists('vendor_id', 'ip_vendor_custom'))
	{
	    $fields = array(
		        'vendor_custom_id' => array(
					'type' => 'INTEGER',
					'default'=>0
                ),
		        'vendor_id' => array(
					'type' => 'INTEGER',
					'default'=>0
                ),
		        'vendor_custom_fieldid' => array(
					'type' => 'INTEGER',
					'default'=>0
                ),
		        'vendor_custom_fieldvalue' => array(
					'type' => 'INTEGER',
					'default'=>0
                ),
		);
		$ci->dbforge->add_column('ip_vendor_custom', $fields);
	}
    if (!$ci->db->field_exists('vendor_id', 'ip_vendor_notes'))
	{
	    $fields = array(
		        'vendor_note_id' => array(
					'type' => 'INTEGER',
					'default'=>0
                ),
		        'vendor_id' => array(
					'type' => 'INTEGER',
					'default'=>0
                ),
		        'vendor_note_date' => array(
					'type' => 'DATE'
                ),
		        'vendor_note' => array(
					'type' => 'LONGTEXT'
                ),
		);
		$ci->dbforge->add_column('ip_vendor_notes', $fields);
    }
    if (!$ci->db->field_exists('status_quotes', 'ip_quotes'))
	{
	    $fields = array(
		        'status_quotes' => array(
					'type' => 'CHAR(10)',
					'default'=>'out'
	            )
		);
		$ci->dbforge->add_column('ip_quotes', $fields);
    }
    if (!$ci->db->field_exists('status_invoices', 'ip_invoices'))
	{
	    $fields = array(
		        'status_invoices' => array(
					'type' => 'CHAR(10)',
					'default'=>'in'
	            )
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
	}
	if (!$ci->db->field_exists('status_quotes', 'ip_invoices'))
	{
	    $fields = array(
		        'status_quotes' => array(
					'type' => 'CHAR(10)',
					'default'=>'out'
	            )
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
    }
	if (!$ci->db->field_exists('do_file', 'ip_invoices'))
	{
	    $fields = array(
		        'do_file' => array(
					'type' => 'VARCHAR(255) NULL',
	            )
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
    }
	if (!$ci->db->field_exists('do_number', 'ip_invoices'))
	{
	    $fields = array(
		        'do_number' => array(
					'type' => 'VARCHAR(255) NULL',
	            )
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
    }
    if (!$ci->db->field_exists('status_payments', 'ip_payments'))
	{
	    $fields = array(
		        'status_payments' => array(
					'type' => 'CHAR(10)',
					'default'=>'in'
	            )
		);
		$ci->dbforge->add_column('ip_payments', $fields);
	}
	if (!$ci->db->field_exists('no_kwitansi', 'ip_payments'))
	{
	    $fields = array(
		        'no_kwitansi' => array(
					'type' => 'VARCHAR(255)'
	            )
		);
		$ci->dbforge->add_column('ip_payments', $fields);
	}

	$cekcp=$ci->db->from('ip_custom_fields')->like('custom_field_table','ip_vendor_custom')->get()->result();
	if(count($cekcp)==0)
	{
		$n['custom_field_table']='ip_vendor_custom';
		$n['custom_field_label']='Contact Person Name';
		$n['custom_field_type']='TEXT';
		$n['custom_field_location']=3;
		$n['custom_field_order']=0;
		$ci->db->insert('ip_custom_fields',$n);
	}

	
	if(!$ci->db->table_exists('ip_supplier'))
	{
		$sql="CREATE TABLE `ip_supplier` (
			`supplier_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`supplier_date_created` datetime NOT NULL,
			`supplier_date_modified` datetime NOT NULL,
			`supplier_name` text,
			`supplier_address_1` text,
			`supplier_address_2` text,
			`supplier_city` text,
			`supplier_state` text,
			`supplier_zip` text,
			`supplier_country` text,
			`supplier_phone` text,
			`supplier_fax` text,
			`supplier_mobile` text,
			`supplier_email` text,
			`supplier_web` text,
			`supplier_vat_id` text,
			`supplier_contact_person_name` text,
			`supplier_tax_code` text,
			`supplier_language` varchar(255) DEFAULT 'system',
			`supplier_active` int(1) NOT NULL DEFAULT '1',
			`supplier_surname` varchar(255) DEFAULT NULL,
			`supplier_avs` varchar(16) DEFAULT NULL,
			`supplier_insurednumber` varchar(30) DEFAULT NULL,
			`supplier_veka` varchar(30) DEFAULT NULL,
			`supplier_birthdate` date DEFAULT NULL,
			`supplier_gender` int(1) DEFAULT '0'
			)";

		$ci->db->query($sql);
	}
	if(!$ci->db->table_exists('ip_po'))
	{
		$sql="CREATE TABLE `ip_po` (
			`po_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`po_number` varchar(255) NULL,
			`po_status` int(1) NOT NULL DEFAULT '1',
			`status_po` char(10) DEFAULT 'in' NULL,
			`po_date_created` DATETIME DEFAULT NULL,
			`po_date_modified` DATETIME DEFAULT NULL,
			`po_date` DATETIME DEFAULT NULL,
			`quotes_id` INT default '0' NULL,
			`supplier_id` INT default '0' NULL,
			`po_currency` char(10) NULL,
			`po_bank_name` varchar(255) NULL,
			`po_account_number` varchar(255) NULL,
			`po_payment_term` varchar(255) NULL,
			`po_freight_term` varchar(255) NULL,
			`po_price_basic` DOUBLE DEFAULT '0' NULL,
			`po_address_all_queries` VARCHAR(255) NULL
			)";

		$ci->db->query($sql);
	}
	if(!$ci->db->table_exists('ip_do'))
	{
		$sql="CREATE TABLE `ip_do` (
			`do_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`invoice_id` INT default '0' NULL,
			`do_number` varchar(255) NULL,
			`do_buyer` varchar(255) NULL,
			`do_fob` varchar(255) NULL,
			`do_delivery_term` varchar(255) NULL,
			`do_ship_date` DATETIME DEFAULT NULL,
			`do_sales_person` varchar(255) NULL,
			`do_shipped_via` varchar(255) NULL
			)";

		$ci->db->query($sql);
	}
	if(!$ci->db->table_exists('ip_cogs'))
	{
		$sql="CREATE TABLE `ip_cogs` (
			`cogs_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`po_id` INT default '0' NULL,
			`item_id` INT default '0' NULL,
			`item_name` varchar(255) NULL,
			`item_quantity` INT default '0' NULL,
			`item_price` DOUBLE default '0' NULL,
			`item_nominal` DOUBLE default '0' NULL,
			`pajak` FLOAT default '0' NULL,
			`item_product_unit` CHAR(10) DEFAULT NULL
			)";

		$ci->db->query($sql);
	}
	if(!$ci->db->table_exists('ip_pajak'))
	{
		$sql="CREATE TABLE `ip_pajak` (
			`pajak_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`po_id` INT default '0' NULL,
			`pajak_name` varchar(255) NULL,
			`pajak_persen` FLOAT default '0' NULL,
			`pajak_nominal` DOUBLE default '0' NULL
			)";

		$ci->db->query($sql);
	}
	if(!$ci->db->table_exists('ip_biaya_lain'))
	{
		$sql="CREATE TABLE `ip_biaya_lain` (
			`lain_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`po_id` INT default '0' NULL,
			`biaya_name` varchar(255) NULL,
			`biaya_persen` FLOAT default '0' NULL,
			`biaya_nominal` DOUBLE default '0' NULL
			)";

		$ci->db->query($sql);
	}

	if (!$ci->db->field_exists('file', 'ip_po'))
	{
	    $fields = array(
		        'file' => array(
					'type' => 'VARCHAR(255)'
	            )
		);
		$ci->dbforge->add_column('ip_po', $fields);
	}
	if (!$ci->db->field_exists('customer_number', 'ip_po'))
	{
	    $fields = array(
		        'customer_number' => array(
					'type' => 'VARCHAR(255)'
	            )
		);
		$ci->dbforge->add_column('ip_po', $fields);
	}
	if (!$ci->db->field_exists('po_id', 'ip_invoices'))
	{
	    $fields = array(
		        'po_id' => array(
					'type' => 'INT',
					'default'=>0
				),
		        'quote_id' => array(
					'type' => 'INT',
					'default'=>0
				),
		);
		$ci->dbforge->add_column('ip_invoices', $fields);
	}
}