<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
/* $key is any you want */
$configValuesMap = array(
  	'credit/email_to_sender/template' 	=>'credit_mail_info_recipient_template',
	'credit/email_to_recipient/template' 	=>'credit_mail_info_recipient_template',
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}
