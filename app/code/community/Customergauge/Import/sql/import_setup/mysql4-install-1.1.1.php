<?php
$installer = $this;
$installer->startSetup();
$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('cgmapping')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `magento_field` varchar(250) NOT NULL default '',
  `cg_field` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$installer->run("
INSERT INTO {$this->getTable('cgmapping')} VALUES (null,'Order_created_at','date_order');
");
$installer->run("
INSERT INTO {$this->getTable('cgmapping')} VALUES (null,'Order_customer_firstname','first_name');
");
$installer->run("
INSERT INTO {$this->getTable('cgmapping')} VALUES (null,'Order_customer_lastname','last_name');
");
$installer->run("
INSERT INTO {$this->getTable('cgmapping')} VALUES (null,'Order_customer_email','email');
");
$installer->run("ALTER TABLE `{$installer->getTable('sales/order')}` ADD `customergauge_import` VARCHAR(255) DEFAULT 'Not Imported' ;");
$installer->run("ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `customergauge_import` VARCHAR(255) DEFAULT 'Not Imported' ;");
$installer->endSetup();
?>