<?php

class Webkul_Customerpartner_Block_Product_View extends Mage_Catalog_Block_Product_View
{

	protected function _prepareLayout()
	{
		$this->getLayout()->getBlock('head')->addCss('css/customerpartner.css');

		return parent::_prepareLayout();
	}

	public function getIsCustomerpartnerProduct()
	{

		$product = $this->getProduct();
		$isCPProduct = Mage::getModel('customerpartner/customerpartner')->isCustomerProduct($product->getId());
		if($isCPProduct){

			$cproduct = Mage::getModel('customerpartner/customerpartner_product')->load($isCPProduct);
			$customer = Mage::getModel('customer/customer')->load($cproduct->getCustomerId());

			return array($customer->getNickname(), $customer->getId());

		}else{

			return false;

		}

	}

	public function getAvatar($customerId)
	{
		$cpBlock = $this->getLayout()->getBlockSingleton('customer/form_edit');

		return $cpBlock->getAvatar($customerId, 100, 70);
	}

}
