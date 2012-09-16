<?php
class Web_QuickCheckout_Model_Observer{
    public function QuickCheckoutRedirect($observer) {
        if (Mage::helper('quickcheckout')->getQuickCheckoutConfig('general/active')) {
	          Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onestep"));
        }
    }	
}