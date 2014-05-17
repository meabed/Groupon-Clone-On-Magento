<?php
class MW_Credit_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
	
    private function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    private function _goBack()
    {
    	return $this->_redirect('checkout/cart');
    }
    
    /**
     * 
     * @return int
     */
	private function _getCredit() 
	{
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		return Mage::getSingleton('credit/creditcustomer')->load($customerId)->getCredit();	
	}
    
    /**
     * 	
     * @param int $credit
     * @return float
     */
    private function _exchangeCreditToMoney($credit)
	{
		$rate = Mage::getStoreConfig('credit/config/credit_money_rate');
		$rate = explode('/',$rate);
	   	$money = ($credit * $rate[1])/$rate[0];
	   	return $money;
	} 
	
	/**
	 * 
	 * @param float $money
	 * @return int
	 */
	private function _exchangeMoneyToCredit($money)
	{
		$rate = Mage::getStoreConfig('credit/config/credit_money_rate');
		$rate = explode('/',$rate);
		$credit = (int)(($money * $rate[0]) / $rate[1]);
		return $credit;
	}
	
	/**
	 * Calculate if credit is gretter than grandTotal
	 * 
	 * @param $credit
	 * @return int
	 */
	private function _getAmountCredit($credit) 
	{
		$addmoney = $this->_exchangeCreditToMoney($this->_getSession()->getCredit());
		// $grandTotal really
		$grandTotal = $this->_getSession()->getQuote()->getGrandTotal() + $addmoney;
		
		$money = $this->_exchangeCreditToMoney($credit);
		if ($money > $grandTotal) {
			$credit = $this->_exchangeMoneyToCredit($grandTotal);
		}
		return $credit;
	}
	
    public function creditPostAction()
    {
		 $credit = (int)$this->getRequest()->getParam('credit_value');
        $credit = abs($credit);
		 $credit = $this->_getAmountCredit($credit);
		 if ($this->getRequest()->getParam('removeCredit') == 1) {
            $credit = '';
        }
    	// no login
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {		
        	$this->_getSession()->addError($this->__('You must to login to use this function.'));
			if ($this->getRequest()->getParam('removeCredit') == 1) {
			 Mage::getSingleton('customer/session')->setGuestcredit(null);
			 $this->_getSession()->addSuccess($this->__('Credit was canceled successfully.'));
			 }
			if(!empty($credit)){	
			$this->_getSession()->addError($this->__('Credit "%s" will applied after you login.', Mage::helper('core')->htmlEscape($credit)));
			Mage::getSingleton('customer/session')->setGuestcredit($credit);
			}else{ Mage::getSingleton('customer/session')->setGuestcredit(null);};
			$this->_goBack();
          	return;
    	}    	
       
        if ($this->getRequest()->getParam('removeCredit') == 1) {
            $credit = '';Mage::getSingleton('customer/session')->setGuestcredit(null);
        }
       
        //echo $credit;die();
        
        // max credit to checkout
        if(	(Mage::helper('credit')->getMaxCreditToCheckOut() < $credit) && 
        	(Mage::helper('credit')->getMaxCreditToCheckOut() != 0) ){
        		
        	$this->_getSession()->addError(
        		$this->__('You only choose the credit less than or equal %s',Mage::helper('credit')->getMaxCreditToCheckOut())
        	);	
        	$this->_goBack();
          	return;
        }
        
        // if credit is gretter than credit customer
        if ($credit > $this->_getCredit()) {
      		$this->_getSession()->addError(
            	$this->__('Credit "%s" is not enough.', Mage::helper('core')->htmlEscape($credit))
          	);
          	$this->_goBack();
          	return;
        }
       
        try {
            $this->_getSession()->setCredit($credit); // set session
            if ($credit) {
        		$this->_getSession()->addSuccess(
            		$this->__('Credit "%s" was applied successfully.', Mage::helper('core')->htmlEscape($credit))
        		);
            } else {
            	$this->_getSession()->addSuccess($this->__('Credit was canceled successfully.'));
        	}

        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not apply credit.'));
        }

        $this->_goBack();
    }
}
?>