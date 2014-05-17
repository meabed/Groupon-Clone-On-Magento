<?php
/**
 * Adminhtml credit transaction history queue grid block (Step3)
 *
 * @category   MW
 * @package    MW_Credit
 * @author Mage World <support@mage-world.com>
 */
class MW_Credit_Block_Adminhtml_Customer_Edit_Tab_Credit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('creditGrid');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('customer')->__('No Credit Transaction Found'));
    }
    
    /**
     * Transaction Detail
     * 
     * @return array
     */
	private function _getTransactionDetail()
    {
    	$arr = array();
    	$collection = Mage::getModel('credit/credithistory')->getCollection()
				->addFilter('customer_id',Mage::registry('current_customer')->getId());
				
		foreach($collection as $credithistory){
			$transactionDetail = MW_Credit_Model_TransactionType::getTransactionDetail($credithistory->getTypeTransaction(),$credithistory->getTransactionDetail(),true); 
			$arr[$credithistory->getId()] = $transactionDetail;
		} 
		return $arr;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('credit/credithistory_collection')
            ->addFieldToFilter('customer_id',Mage::registry('current_customer')->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('credit_history_id', array(
            'header'    =>  Mage::helper('credit')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'credit_history_id',
            'width'     =>  10
        ));
        
      	$this->addColumn('created_time', array(
            'header'    =>  Mage::helper('credit')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'created_time',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));
        
    	$this->addColumn('type_transaction', array(
          	'header'    => Mage::helper('credit')->__('Type of Transaction'),
          	'align'     =>'left',
          	'index'     => 'type_transaction',
		  	'width'     => '250px',
		  	'type'      => 'options',
          	'options'   => MW_Credit_Model_TransactionType::getOptionArray(),
      	));

      	/**
      	 * Transaction Detail
      	 */
        $this->addColumn('transaction_detail', array(
            'header'    =>  Mage::helper('credit')->__('Transaction Details'),
            'align'     =>  'left',
        	'width'		=>  400,
            'index'     =>  'credit_history_id',
        	'type'      => 'options',
          	'options'   => $this->_getTransactionDetail(),
        ));

        $this->addColumn('beginning_transaction', array(
          	'header'    => Mage::helper('credit')->__('Beginning Transaction'),
          	'index'     => 'beginning_transaction',
        	'type'  => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
      	));
      	
      	$this->addColumn('end_transaction', array(
          	'header'    => Mage::helper('credit')->__('Ending Transaction'),
          	'index'     => 'end_transaction',
      		'type'  => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
      	));
        
        $this->addColumn('amount', array(
            'header'    =>  Mage::helper('credit')->__('Amount'),
        	'align'     =>  'center',
            'index'     =>  'amount',
        ));
        
        $this->addColumn('status', array(
          	'header'    => Mage::helper('credit')->__('Status'),
          	'align'     =>'center',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('credit/OrderStatus')->getOptionArray(),
      	));
        
        return parent::_prepareColumns();
    }

}
