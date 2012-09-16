<?php


$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();
try{
	$installer->run("
			ALTER TABLE `{$installer->getTable('sales/quote')}`
			ADD `quickcheckout_heard` TEXT NULL ,
			ADD `quickcheckout_comment` TEXT NULL 
			
		");
}catch(Exception $e){
	if(strpos($e, 'Column already exists') === false){
		throw $e;
	}
}
try{
	$installer->run("
			ALTER TABLE `{$installer->getTable('sales/order')}`
			ADD `quickcheckout_heard` TEXT NULL ,
			ADD `quickcheckout_comment` TEXT NULL
			
		");
}catch(Exception $e){
	if(strpos($e, 'Column already exists') === false){
		throw $e;
	}
}
$installer->endSetup();