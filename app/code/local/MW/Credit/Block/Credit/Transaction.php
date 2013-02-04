<?php
class MW_Credit_Block_Credit_Transaction extends Mage_Core_Block_Template
{
	
	public function __construct()
    {
        parent::__construct();

		// get collection follow filter customer_id
        $credits = Mage::getModel('credit/credithistory')->getCollection()
					->addFilter('customer_id',$this->_getCustomer()->getId())
					->setOrder('credit_history_id', 'DESC');

        $this->setCreditHistory($credits);	// set data for display to frontend
    }
    
	private function _getCustomer()
	{
		return Mage::getSingleton("customer/session")->getCustomer();
	}
	
	public function getTypeLabel($type)
	{
		return MW_Credit_Model_TransactionType::getLabel($type);
	}
	
	public function getTransactionDetail($type, $detail)
	{
		return MW_Credit_Model_TransactionType::getTransactionDetail($type,$detail);
	}
	
	public function getStatusText($status)
	{
		return MW_Credit_Model_OrderStatus::getLabel($status);
	}

	// prepare navigation
	public function _prepareLayout()
    {
		//return parent::_prepareLayout();
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_credit_transaction')
					  ->setCollection($this->getCreditHistory());	// set data for navigation
        $this->setChild('pager', $pager);
        return $this;
    }
	
	// get navigation
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}