<?php
class MW_Credit_Model_System_Config_Entier extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
    	/*
        $value = $this->getValue();        
    	if (!Zend_Validate::is($value, 'NotEmpty')) {
    		Mage::throwException(Mage::helper('credit')->__("A value for max friend to send credit is required."));
        }
        if (!Zend_Validate::is($value, 'Digits')) {
        	Mage::throwException(Mage::helper('credit')->__("'%s' is not an integer.", $value));
        }
        $validator = new Zend_Validate_GreaterThan(0);
		if (!$validator->isValid($value)) {
        	Mage::throwException(Mage::helper('credit')->__("value '%s' is not valid.", $value));
        }
        return $this;
        */
    }
}