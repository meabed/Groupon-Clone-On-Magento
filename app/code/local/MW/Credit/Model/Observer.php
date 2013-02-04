<?php
class MW_Credit_Model_Observer
{
	
	private function _getSession() 
	{
		return Mage::getSingleton('checkout/session');
	}
	
	private function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	} 
	
	/**
	 * Save Credit for admin Change
	 * 
	 * @param $observer
	 * @return void
	 */
    public function saveCredit($observer)
    {
    	$customer = $observer->getCustomer();
    	$request = $observer->getRequest();
    	
    	$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer->getId());
    	$oldCredit = $creditcustomer->getCredit();
    	$amountCredit = (int)$request->getParam('amount_credit');
    	$isRefundProduct = $request->getParam('is_refund_product');
    	$creditmemo = '';
    	
    	if($amountCredit == 0) {
    		return;
    	}
    	
    	if (!$isRefundProduct){
			$typeTransaction = MW_Credit_Model_TransactionType::ADMIN_CHANGE;
			$newCredit = $oldCredit + $amountCredit;
    	}else{ 
    		$typeTransaction = MW_Credit_Model_TransactionType::REFUND_PRODUCT;
    		$creditmemo = $request->getParam('creditmemo');
    		$amountCredit = abs($amountCredit);
    		$newCredit = $oldCredit + $amountCredit;
    	}
    	
    	// In Database, credit is:  int(11) unsigned NOT NULL default '0'
	    $creditcustomer->setData('credit',$newCredit)->save();

    	// Save history transaction for customer
        $historyData = array('type_transaction'=>$typeTransaction, 
            			     'transaction_detail'=>$creditmemo, 
            				 'amount'=>$amountCredit, 
            				 'beginning_transaction'=>$oldCredit,
        					 'end_transaction'=>$newCredit,
            			     'created_time'=>now());
   		Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData,$customer->getId());
    }
	
    public function editGrandTotal($observer)
    {
    	if(Mage::helper('credit')->getEnabled()){
	    	$quote = $observer->getQuote();
	    	
	    	$credit = $this->_getSession()->getCredit();
	    	if($credit) {
		    	$money = Mage::helper('credit')->exchangeCreditToMoney($credit);
				
				$quote->setTotalsCollectedFlag(false);
				$quote->getShippingAddress()->setTotalAmount('credit_amount',-$money);
				$quote->setTotalsCollectedFlag(true);
	    	}
    	}
	}
	
	/**
	 * Save Credit for Checkout
	 * 
	 * @param $observer
	 * @return void
	 */
	public function saveOrderAfter($observer)
    {
        if(Mage::helper('credit')->getEnabled()) {
	    	$order = $observer->getOrder();
	    	$customerId = $this->_getCustomer()->getId();
	    	if($customerId){            
	            //Subtract credit points of customer and save to order if customer use this credit to checkout
	            $amountCredit = $this->_getSession()->getCredit();
	            if($amountCredit){
	            	
	            	$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customerId);
	            	$oldCredit = $creditcustomer->getCredit();
	            	$newCredit = $oldCredit - $amountCredit;
	            	
	            	// Save history transaction
	            	$historyData = array('type_transaction'=>MW_Credit_Model_TransactionType::USE_TO_CHECKOUT, 
	            					     'transaction_detail'=>$order->getIncrementId(), 
	            						 'amount'=>-$amountCredit, 
	            						 'beginning_transaction'=>$oldCredit,
	            						 'end_transaction'=>$newCredit,
	            					     'created_time'=>now());
	            	Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData);
	            	
	            	//Subtract credit of customer
	            	$creditcustomer->setCredit($newCredit)->save();
	            	
	           		//Save credit to order
            		$orderData = array('order_id'=>$order->getId(),
            				   		   'credit'=>-$amountCredit, 
            						   'money'=>Mage::helper('credit')->exchangeCreditToMoney($amountCredit),
            						   'credit_money_rate'=>Mage::helper('credit')->getCreditMoneyRateConfig());
            		Mage::getModel("credit/creditorder")->saveCreditOrder($orderData);    		
	            }
	            
	            //Reset credit in Session
	            $this->_getSession()->unsetData('credit');
	    	}
		}
    }
    
    public function updateStatus($observer)
    {		
		$guestcredit=Mage::getSingleton('customer/session')->getGuestcredit();
		if(!empty($guestcredit)){
			if(	(Mage::helper('credit')->getMaxCreditToCheckOut() < $guestcredit) && 
        	(Mage::helper('credit')->getMaxCreditToCheckOut() != 0) ){
				$guestcredit=Mage::helper('credit')->getMaxCreditToCheckOut();
			};			
			$total_amount=Mage::getModel('credit/creditcustomer')->load($observer->getModel()->getId())->getCredit();	
			
			 if ($guestcredit > $total_amount) {
					$guestcredit= $total_amount;
			 };			 
			Mage::getSingleton('checkout/session')->setCredit($guestcredit);			
			Mage::getSingleton('customer/session')->setGuestcredit(null);
		};
	
     	if(Mage::helper('credit')->getEnabled()) {
			$creditcustomer = Mage::getModel('credit/creditcustomer')->load($observer->getModel()->getId());
			// get all transaction you have and status is pending to process
			$collection = Mage::getModel('credit/credithistory')->getCollection()
					->addFieldToFilter('customer_id',$creditcustomer->getId())
					->addFieldToFilter('status',array(MW_Credit_Model_OrderStatus::PENDING));
		
     		foreach($collection as $credithistory) {
				switch($credithistory->getTypeTransaction()) {										
					case MW_Credit_Model_TransactionType::REFUND_PRODUCT:
						// current no action
						break;
						
					case MW_Credit_Model_TransactionType::USE_TO_CHECKOUT:
						$order = Mage::getModel("sales/order")->loadByIncrementId($credithistory->getTransactionDetail());
						$status = $credithistory->getStatus();
						if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED) {
							if($order->hasInvoices()) {
								$status = MW_Credit_Model_OrderStatus::COMPLETE;
								$credithistory->setStatus($status)->save();
							}
						}else{
							$status = MW_Credit_Model_OrderStatus::CANCELED;
							$credithistory->setStatus($status)->save();
							
							// refund credit, add again credit
							$amountcredit = $creditcustomer->getCredit() - $credithistory->getAmount();
							$creditcustomer->setCredit($amountcredit)->save();
						}
						break;
				}
			}		
     	}
    }
    
    /**
     * 
     * @param $observer
     * @return void
     */
    public function initializeCredit($observer)
    {
    	$customer = $observer->getCustomer();
		$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer->getId());
     	if( Mage::helper('credit')->getEnabled() && !($creditcustomer->getId()) ) {
		   //Add credit to new customer
           $customerData = array('customer_id'=>$customer->getId(),
            	   		       	  'credit'=>0, 
            			          'parent_id'=>0);
           Mage::getModel("credit/creditcustomer")->saveCreditCustomer($customerData); 
		}
    }
	
	/**
	 * Exchange reward points to Credit. And then credit will adding
	 * 
	 * @param $observer
	 * @return $result boolean
	 */
	public function exchange($observer)
	{
		$result = false;
		$customer 		= $observer->getCustomer();		// Instance of Mage_Core_Model_Customer
		$amountCredit 	= $observer->getCredit();		// (int) The Reward 
		
		try{
			if($amountCredit < 0) throw('credit is incorrect');
			$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer->getId());
			$oldCredit = $creditcustomer->getCredit();
			
			// Save history transaction for customer
            $historyData = array('type_transaction'=>MW_Credit_Model_TransactionType::SEND_TO_FRIEND, 
            				     'transaction_detail'=>'', 
            					 'amount'=>$amountCredit, 
            					 'beginning_transaction'=>$oldCredit,
            					 'end_transaction'=>$newCredit,
            				     'created_time'=>now());
            Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData);
            
            $newCredit = $oldCredit + $amountCredit;
            $creditcustomer->addCredit($newCredit)->save();
			
            $result = true;
			
		}catch(Mage_Core_Exception $e){
			$result = false;
		}
		
		Mage::dispatchEvent('mw_credit_exchange_rewardpoints_to_credit_after', array(
			'customer'	=> $customer,
            'result'    => $result,
        ));
	}
	
    // For debug
    public function exchangeSuccess($observer)
	{
		Mage::log($observer->getResult());
	}
}