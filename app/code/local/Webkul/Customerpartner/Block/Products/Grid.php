<?php

class Webkul_Customerpartner_Block_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('customersProducts');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');

        $this->_emptyText = Mage::helper('customerpartner')->__('No Products Found.');
    }

    protected function _prepareCollection()
    {
    	$showUnapprovedOnly = $this->getRequest()->getParam('ff');

        $collection = Mage::getResourceModel('customerpartner/customerpartner_product_collection')->addAttributeToSelect('*');

		if(!is_null($showUnapprovedOnly)){
			$collection->addAttributeToFilter('product_id', 0);
		}

        $this->setCollection($collection);

        parent::_prepareCollection();

		$customerModel = Mage::getModel('customer/customer');
		$logic = Mage::getModel('customerpartner/customerpartner');
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
		//Modify loaded collection
        foreach ($this->getCollection() as $item) {

            $customer = $customerModel->load($item->getCustomerId());
            $item->customer_name = sprintf('<a href="%s" title="View Customer\'s Profile">%s</a>',
            								$this->getUrl('adminhtml/customer/edit/id/' . $item->getCustomerId()),
            								$customer->getName()
            							  );

            $item->status = sprintf('<a href="%s" title="Click to Approve" onclick="return confirm(\'You sure?\')">Unnaproved</a>',
            						$this->getUrl('/products/approve/id/' . $item->getId())
            					   );
			//If product exists, place link to view it
            if(!(is_null($item->getProductId())) && $item->getProductId() != 0){
				$item->status = sprintf('<a href="%s" title="View product">Approved</a>',
										 $this->getUrl('adminhtml/catalog_product/edit/id/' . $item->getProductId())
									    );

				#also show the current name, price , stock and weight
				$product = Mage::getModel('catalog/product')->load($item->getProductId());

				$stock_inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());

				$item->name = $product->getName();
				$item->weight = $product->getWeight();
				$item->price = $product->getPrice();
				$item->stock = $stock_inventory->getQty();


            }
			/****** here i code for mysql prefix checking and execute code*****/
			$sql = sprintf('SELECT SUM(quantity) AS qty_sold FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $item->getId());
			/** end **/
            #$sql = sprintf('SELECT SUM(quantity) AS qty_sold FROM customerpartner_aux WHERE product_id = %d', $item->getId());
            $qtySold = $logic->customQuery($sql)->fetch();

			$item->qty_sold = (int)$qtySold['qty_sold'];
			
			/****** here i code for mysql prefix checking and execute code*****/
			 $sqlAE = sprintf('SELECT SUM(amount_earned) AS amount_earned FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $item->getId());
			/** end **/
            #$sqlAE = sprintf('SELECT SUM(amount_earned) AS amount_earned FROM customerpartner_aux WHERE product_id = %d', $item->getId());
            $amountEarned = $logic->customQuery($sqlAE)->fetch();

			$item->amount_earned = $amountEarned['amount_earned'];

			if ( $item->amount_earned > 0 ) {
				#link to orders
				$item->orders = sprintf('<a href="%s" title="Click to see">Orders</a>',
										 $this->getUrl('adminhtml/sales_order/index/partner_product_id/' . $item->getId())
									    );

			}
			/****** here i code for mysql prefix checking and execute code*****/
			$sqlAO = sprintf('SELECT SUM(amount_owed) AS amount_owed FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d', $item->getId());
			/** end **/
            #$sqlAO = sprintf('SELECT SUM(amount_owed) AS amount_owed FROM customerpartner_aux WHERE product_id = %d', $item->getId());
            $amountOwed = $logic->customQuery($sqlAO)->fetch();

			$item->amount_owed = $amountOwed['amount_owed'];

			if ( $item->amount_owed > 0 ) {
				#link to clear owed
				$item->act = sprintf('<a href="%s" title="Click to clear amount owed" onclick="return confirm(\'You sure?\')">Clear</a>',
										 $this->getUrl('customerpartner/products/clearo/id/' . $item->getId())
									    );

			}

			#cleared_act
			
			/****** here i code for mysql prefix checking and execute code*****/
			$sqlAO = sprintf('SELECT cleared_at FROM '.$mysqlprefix.'customerpartner_aux WHERE product_id = %d ORDER BY cleared_at DESC LIMIT 1', $item->getId());
			/** end **/
            #$sqlAO = sprintf('SELECT cleared_at FROM customerpartner_aux WHERE product_id = %d ORDER BY cleared_at DESC LIMIT 1', $item->getId());
            $cleared_act = $logic->customQuery($sqlAO)->fetch();

			if ( isset($cleared_act) && isset($cleared_act['cleared_at']) && $cleared_act['cleared_at'] != '0000-00-00 00:00:00' ) {
				$item->cleared_at = $cleared_act['cleared_at'];
			}


        }


        return parent::_prepareCollection();
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareColumns()
    {
    	$store = $this->_getStore();

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customerpartner')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number'
        ));
//        $this->addColumn('customer_id', array(
//            'header'    => Mage::helper('customerpartner')->__('Customer ID'),
//            'index'     => 'customer_id',
//            'type'  => 'number'
//        ));
        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('customerpartner')->__('Customer Name'),
            'index'     => 'customer_name',
            'type'  => 'text',
			'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('customerpartner')->__('Name'),
            'index'     => 'name',
            'type'  => 'string'
        ));
/*        $this->addColumn('description', array(
            'header'    => Mage::helper('customerpartner')->__('Description'),
            'index'     => 'description',
            'type'  => 'string',
			'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('short_description', array(
            'header'    => Mage::helper('customerpartner')->__('Short Description'),
            'index'     => 'short_description',
            'type'  => 'string',
			'filter'    => false,
            'sortable'  => false
        ));*/
        $this->addColumn('price', array(
            'header'    => Mage::helper('customerpartner')->__('Price'),
            'index'     => 'price',
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode()
        ));
        $this->addColumn('stock', array(
            'header'    => Mage::helper('customerpartner')->__('Stock'),
            'index'     => 'stock',
            'type'  => 'number'
        ));
        $this->addColumn('weight', array(
            'header'    => Mage::helper('customerpartner')->__('Weight'),
            'index'     => 'weight',
            'type'  => 'number'
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('customerpartner')->__('Status'),
            'index'     => 'status',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('orders', array(
            'header'    => Mage::helper('customerpartner')->__('Orders'),
            'index'     => 'orders',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('qty_sold', array(
            'header'    => Mage::helper('customerpartner')->__('Qty. Sold'),
            'index'     => 'qty_sold',
            'type'  => 'number',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('amount_earned', array(
            'header'    => Mage::helper('customerpartner')->__('Earned'),
            'index'     => 'amount_earned',
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('amount_owed', array(
            'header'    => Mage::helper('customerpartner')->__('Owed'),
            'index'     => 'amount_owed',
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode()
        ));
        $this->addColumn('act', array(
            'header'    => Mage::helper('customerpartner')->__(''),
            'index'     => 'act',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('cleared_at', array(
            'header'    => Mage::helper('customerpartner')->__('Last cleared date'),
            'index'     => 'cleared_at',
            'type'  => 'datetime'
        ));
        /*$this->addColumn('status', array(
            'header'    => Mage::helper('customerpartner')->__('Status'),
            'index'     => 'status',
            'type'  => 'options',
            'options' =>array('unaproved'=>'Unaproved', 'approved'=>'Approved')
        ));*/
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('customerpartner')->__('Created'),
            'index'     => 'created_at',
            'type'  => 'datetime'
        ));



        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

}
