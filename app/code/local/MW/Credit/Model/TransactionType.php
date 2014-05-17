<?php

class MW_Credit_Model_TransactionType extends Varien_Object
{
    const ADMIN_CHANGE 			= 1;
    const REFUND_PRODUCT 		= 2;
    const RECIVE_FROM_FRIEND 	= 3;
	const SEND_TO_FRIEND		= 4;
	const EXCHANGE_TO_POINT		= 5;	
	const USE_TO_CHECKOUT		= 6;
	const BUY_CREDIT			= 7;
		
    static public function getOptionArray()
    {
        return array(
			self::ADMIN_CHANGE   			=> Mage::helper('credit')->__('Changed By Admin'),	
			self::REFUND_PRODUCT    		=> Mage::helper('credit')->__('Refund Product'),
    		self::RECIVE_FROM_FRIEND    	=> Mage::helper('credit')->__('Recive From Your Friend'),
    		self::SEND_TO_FRIEND   			=> Mage::helper('credit')->__('Send Point To Friend'),
    		self::EXCHANGE_TO_POINT   		=> Mage::helper('credit')->__('Exchange Credit To Point'),
    		self::USE_TO_CHECKOUT			=> Mage::helper('credit')->__('Use to checkout'),
    		self::BUY_CREDIT				=> Mage::helper('credit')->__('Buy Credit')
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    
	static public function getTransactionDetail($type, $detail, $is_admin=false)
    {
    	$result = "";
    	if ($is_admin) $url = "adminhtml/sales_order/view";
    	else  $url = "sales/order/view";
    	
    	switch($type)
    	{
    		case self::ADMIN_CHANGE:
    			$result = self::getLabel($type);
    			break;
    		case self::REFUND_PRODUCT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You refurn to order : <b><a href=\"%s\">#%s</a></b>",
    											  	  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		case self::RECIVE_FROM_FRIEND:
    			$customer = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('credit')->__("You recive credit from <b>%s</b>",$customer->getEmail());
    			break;
    		case self::SEND_TO_FRIEND:
    			$customer = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('credit')->__("You send credit to <b>%s</b>",$customer->getEmail());
    			break;
    		case self::EXCHANGE_TO_POINT:
    			$result = $this->getLabel($type);
    			break;
    		case self::USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You use credit to check out order : <b><a href=\"%s\">#%s</a></b>",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		case self::BUY_CREDIT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You bye credit to order : <b><a href=\"%s\">#%s</a></b>",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    	}
    	return $result;
    }
}