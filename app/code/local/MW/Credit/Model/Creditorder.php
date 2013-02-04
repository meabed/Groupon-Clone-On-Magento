<?php

class MW_Credit_Model_Creditorder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('credit/creditorder');
    }
    
   	public function saveCreditOrder($orderData)
    {
    	$collection = Mage::getModel('credit/creditorder')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'INSERT INTO '.$collection->getTable('creditorder').' 
    				VALUES('.$orderData['order_id'].','.$orderData['credit'].','.$orderData['money'].',\''.$orderData['credit_money_rate'].'\')';
		$write->query($sql);
    }
}