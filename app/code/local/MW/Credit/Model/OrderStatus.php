<?php

class MW_Credit_Model_Orderstatus extends Varien_Object
{
	const PENDING				= 0;
    const COMPLETE				= 1;
    const CANCELED				= 2;

    static public function getOptionArray()
    {
        return array(
            self::PENDING    				=> Mage::helper('credit')->__('Pending'),
            self::COMPLETE	    			=> Mage::helper('credit')->__('Complete'),
            self::CANCELED  			 	=> Mage::helper('credit')->__('Canceled'),
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
}