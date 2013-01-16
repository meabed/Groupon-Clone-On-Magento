<?php

class Webkul_Customerpartner_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

	protected function _prepareCollection()
    {

        //TODO: add full name logic
       /* $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
            ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->addExpressionAttributeToSelect('billing_name',
                'CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',
                array('billing_firstname', 'billing_lastname'))
            ->addExpressionAttributeToSelect('shipping_name',
                'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                array('shipping_firstname', 'shipping_lastname'));*/

	$collection = Mage::getResourceModel($this->_getCollectionClass());

		
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
        $partner_product_id = $this->getRequest()->getParam('partner_product_id');
        if ( $partner_product_id != null ) {
        	#get the orders id where this products was sold
			$orders = array();

        	$logic = Mage::getModel('customerpartner/customerpartner');
			/****** here i code for mysql prefix checking and execute code*****/
			$row = $logic->customQuery(sprintf('SELECT DISTINCT order_id FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $partner_product_id));
			/** end **/
			#$row = $logic->customQuery(sprintf('SELECT DISTINCT order_id FROM customerpartner_aux WHERE product_id = %d', $partner_product_id));
			
        	while( $order = $row->fetch() ) {
        		$orders[] = $order['order_id'];
        	}

           	#$this->getCollection()->addAttributeToFilter('entity_id', array('in' => $orders));
           	$collection->addAttributeToFilter('entity_id', array('in' => $orders));

        }

        $this->setCollection($collection);

        #return $this->getCollection($collection);
        #return parent::_prepareCollection();
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

    }
}
?>
