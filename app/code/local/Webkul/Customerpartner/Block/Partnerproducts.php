<?php

class Webkul_Customerpartner_Block_Partnerproducts extends Mage_Customer_Block_Account_Dashboard
{

	protected $_productsCollection = null;

	public function getProducts()
	{
		if ( $this->_productsCollection == null ) {

			$customerId = $this->getCustomer()->getId();
			$products = Mage::getResourceModel('customerpartner/customerpartner_product_collection')
						->addAttributeToSelect('*')
						->addAttributeToFilter('customer_id', $customerId)
						->load()
						->toArray();

			$this->_productsCollection = $products;
		}

		$products = $this->_productsCollection;

		foreach ($products as $key => $p) {
			if ($p['product_id'] != 0) {

				#also show the current name, price , stock and weight
				$product = Mage::getModel('catalog/product')->load($p['product_id']);
				#print_r($product);
				$stock_inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p['product_id']);

				$p['name'] = $product->getName();
				$p['weight'] = $product->getWeight();
				$p['price'] = $product->getPrice();
				$p['stock'] = $stock_inventory->getQty();
				$p['image']=$product->getImageUrl();
				#$p['sku']=$product->getSku();
				$products[$key] = $p;
			}
		}

		return $products;

	}


	public function getQtySoldandEarned($product_id)
	{
		$result = array();

		if ( $this->_logic == null ) {
			$this->_logic = Mage::getModel('customerpartner/customerpartner');
		}
		
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
		/****** here i code for mysql prefix checking and execute code*****/
		$sql = sprintf('SELECT SUM(quantity) AS qty_sold FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $product_id);
		/** end **/
        #$sql = sprintf('SELECT SUM(quantity) AS qty_sold FROM customerpartner_aux WHERE product_id = %d', $product_id);
        $qtySold = $this->_logic->customQuery($sql)->fetch();

        $result['qtySold'] = (int)$qtySold['qty_sold'];
		/****** here i code for mysql prefix checking and execute code*****/
		$sqlAE = sprintf('SELECT SUM(amount_earned) AS amount_earned FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $product_id);
		/** end **/
       # $sqlAE = sprintf('SELECT SUM(amount_earned) AS amount_earned FROM customerpartner_aux WHERE product_id = %d', $product_id);
        $amountEarned = $this->_logic->customQuery($sqlAE)->fetch();

		$result['earned'] = $amountEarned['amount_earned'];

		/****** here i code for mysql prefix checking and execute code*****/
		$sqlAO = sprintf('SELECT SUM(amount_owed) AS amount_owed FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $product_id);
		/** end **/
		#$sqlAO = sprintf('SELECT SUM(amount_owed) AS amount_owed FROM customerpartner_aux WHERE product_id = %d', $product_id);
        $amountOwed = $this->_logic->customQuery($sqlAO)->fetch();

		$result['owed'] = $amountOwed['amount_owed'];

		return $result;
	}


}

