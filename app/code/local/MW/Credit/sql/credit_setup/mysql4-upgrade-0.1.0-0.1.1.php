<?php

$installer = $this;
$installer->startSetup();

$customers = Mage::getModel('customer/customer')->getCollection();

$sql ="";
foreach($customers as $customer)
{
	$sql .="INSERT INTO {$this->getTable('mw_credit_customer')} 
				VALUES(".$customer->getId().",0,0);";
}
$installer->run($sql);

$installer->endSetup();