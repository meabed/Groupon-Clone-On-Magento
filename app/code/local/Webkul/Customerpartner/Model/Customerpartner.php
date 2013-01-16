<?php

class Webkul_Customerpartner_Model_Customerpartner extends Varien_Object
{

	public function createPartnerProduct(array $args = array())
	{
		if(empty($args)){
			return false;
		}

		$product = Mage::getModel('customerpartner/customerpartner_product');
		$product->setName($args['name']);
		$product->setDescription($args['description']);
		$product->setShortDescription($args['short_description']);
		$product->setPrice($args['price']);
		$product->setStock($args['stock']);
		$product->setWeight($args['weight']);
		$product->setCustomerId($args['customer_id']);
		$product->setProductId(0);
		$product->save();

		return $product->getId();
	}

	public function customQuery($query = '', $cnnType = 'read')
	{
		// Fetch write database connection that is used in Mage_Core module
		$db = Mage::getSingleton('core/resource')->getConnection('core_' . $cnnType);

		// Now $db is an instance of Zend_Db_Adapter_Abstract
		$queryResult = $db->query($query);

		return $queryResult;
	}

	public function isCustomerProduct($magentoProductId)
	{
		$product =
			Mage::getResourceModel('customerpartner/customerpartner_product_collection')
			->addAttributeToSelect('*')
			->addAttributeToFilter('product_id', $magentoProductId)->load();
		foreach($product as $p){
			return $p->getId();
		}
	}

}
