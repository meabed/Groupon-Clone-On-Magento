<?php
require_once BP.DS.'app'.DS.'code'.DS.'core'.DS.'Mage'.DS.'Checkout'.DS.'controllers'.DS.'OnepageController.php';
class Web_QuickCheckout_OnestepController extends Mage_Checkout_OnepageController
{
    //protected $redirect_before_review = true;
    
    public function indexAction()
    {
        
        if (!Mage::helper('quickcheckout')->getQuickCheckoutConfig('general/active')) {
            $this->_redirect('checkout/onepage');
            return;
        }
        Mage::getSingleton('checkout/session')->setRBR(true);
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        if (!count(Mage::getSingleton('customer/session')->getCustomer()->getAddresses())) {
            $this->setDefaultCountryId();
        }
        
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array(
            '_secure' => true
        )));
        $this->getOnepage()->initCheckout();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            //$this->getOnepage()->saveCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST);
            $this->getOnepage()->saveCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST);
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('quickcheckout')->getQuickCheckoutTitle());
        $this->renderLayout();
    }
    
    public function loginPostAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = Mage::getSingleton('customer/session');
        $message = '';
        $result  = array();
        
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost();
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                }
                catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', Mage::helper('customer')->getEmailConfirmationUrl($login['username']));
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->setUsername($login['username']);
                }
                catch (Exception $e) {
                    $message = $e->getMessage();
                }
            } else {
                $message = $this->__('Login and password are required');
            }
        }
        if ($message) {
            $result['error'] = $message;
        } else {
            $result['redirect'] = 1;
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
     public function forgotpassPostAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $message=$this->__('Invalid email address.');
            }
            else{
                 $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
    
                if ($customer->getId()) {
                    try {
                        $newPassword = $customer->generatePassword();
                        $customer->changePassword($newPassword, false);
                        $customer->sendPasswordReminderEmail();
                        $message=$this->__('A new password has been sent.');
                    }
                    catch (Exception $e){
                         $message=$e->getMessage();
                    }
                } else {
                    $message=$this->__('This email address was not found in our records.');
                }
            }
        } else {
            $message=$this->__('Please enter your email.');
        }
            $result['error'] = $message;
       
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function reloadReviewAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = array();
        try {
            $result['review'] = $this->_getReviewHtml();
        }
        catch (Exception $e) {
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function switchMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        
        $method = $this->getRequest()->getPost('method');
        if ($this->getRequest()->isPost() && $method)
            $this->getOnepage()->saveCheckoutMethod($method);
    }
    
    public function reloadPaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = array();
        try {
            $result['payment'] = $this->_getPaymentMethodsHtml();
        }
        catch (Exception $e) {
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
           
        $result = array();
        $data   = $this->getRequest()->getPost();
        //print_r($data);exit;
        if ($data) {
            if ($data['use_for'] == 'billing') {
                Mage::getSingleton('checkout/session')->setData('use_for_shipping', false);
                try {
                    $this->getQuote()->getBillingAddress()->setCountryId($data['country_id'])->setPostcode($data['postcode'])->setRegionId($data['region_id'])->save();
                    $this->getQuote()->getShippingAddress()->setCountryId($data['country_id'])->setPostcode($data['postcode'])->setRegionId($data['region_id'])->save();
                    $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                    $this->getQuote()->collectTotals()->save();
                    $result['shippingMethod'] = $this->_getShippingMethodsHtml();
                    $result['payment']        = $this->_getPaymentMethodsHtml();
                }
                catch (Exception $e) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $e->getMessage();
                }
            } else {
                Mage::getSingleton('checkout/session')->setData('use_for_shipping', true);
                try {
                    $this->getQuote()->getBillingAddress()->setCountryId($data['country_id'])->setPostcode($data['postcode'])->setRegionId($data['region_id'])->save();
                    $result['payment'] = $this->_getPaymentMethodsHtml();
                }
                catch (Exception $e) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $e->getMessage();
                }
            }
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
        public function save_Before_ReviewAction()
    {
          if ($this->_expireAjax()) {
            return;
        }
           
        $result = array();
        $billdata   = $this->getRequest()->getPost();
        //print_r($data);exit;
        if ($billdata) {
            // $this->getResponse()->setBody(Zend_Json::encode($data['use_for']));return;
            $data = $this->getRequest()->getPost('billing', array());
            if (!$data['use_for_shipping']) {
                Mage::getSingleton('checkout/session')->setData('use_for_shipping', false);
                   $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
                    if (isset($data['email'])) {
                            $data['email'] = trim($data['email']);
                    }
                    $this->getOnepage()->saveBilling($data, $customerAddressId);
                    $customerAddressId2 = $this->getRequest()->getPost('shipping_address_id', false);
                    $this->getOnepage()->saveShipping($data, $customerAddressId2);
                    $this->getQuote()->collectTotals()->save();
            } else {
                    $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
                    if (isset($data['email'])) {
                            $data['email'] = trim($data['email']);
                    }
                    $this->getOnepage()->saveBilling($data, $customerAddressId);
                    if($data2 = $this->getRequest()->getPost('shipping', array())){
                         $customerAddressId2 = $this->getRequest()->getPost('shipping_address_id', false);
                        $this->getOnepage()->saveShipping($data2, $customerAddressId2);
                    }
                    $this->getQuote()->collectTotals()->save();
        }
        }
           $result['success'] = true;
            $result['error'] = false;
            $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }
    
    
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        Mage::getSingleton('checkout/session')->setData('use_for_shipping', true);
        $result = array();
        $data   = $this->getRequest()->getPost();
        if ($data) {
            try {
                   $this->getQuote()->getShippingAddress()->setCountryId($data['country_id'])->setPostcode($data['postcode'])->setRegionId($data['region_id'])->save();
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->getQuote()->collectTotals()->save();
                $result['shippingMethod'] = $this->_getShippingMethodsHtml();
            }
            catch (Exception $e) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $e->getMessage();
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function saveShippingMethodAction() {
  if($this->_expireAjax()) {
      return;
  }
  $result = array();
  $data = $this->getRequest()->getPost();
  if($data) {
      try{
    $return = $this->getOnepage()->saveShippingMethod($data['shipping_method']);
    if(!$return) {
        Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
        $this->getQuote()->collectTotals()->save();
    }
    $result['payment'] = $this->_getPaymentMethodsHtml();
    $result['review'] = $this->_getReviewHtml();
      } catch(Exception $e) {
        $result['success'] = false;
        $result['error'] = true;
        $result['error_messages'] = $e->getMessage();
      }
  }
  $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = array();
        $data   = $this->getRequest()->getPost();
        if ($data) {
            try {
                $this->getQuote()->getBillingAddress()->setPaymentMethod($data['method'])->save();
                $this->getQuote()->getPayment()->setMethod($data['method'])->save();
                $this->getQuote()->collectTotals();
                  Mage::getSingleton('checkout/session')->setRBR(true);
                 if($this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl()){
                     Mage::getSingleton('checkout/session')->setRBR(false);
                 }
                //$result['test']   = $this->getQuote()->getPayment()->getMethod();
                $result['review'] = $this->_getReviewHtml();
            }
            catch (Exception $e) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $e->getMessage();
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
     /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

  
    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
      public function cartupdateAction()
    {
        $a=$this->getRequest()->getParam('productid');
        $b=$this->getRequest()->getParam('update');
        $quote = $this->getQuote();
        $product = $quote->getItemById($a);
        if($b=="inc" && $a ){
          
            $qty = intval($product->getQty()+1);
            $maximumQty = intval(Mage::getModel('catalog/product')->load($product->getProductId())->getStockItem()->getMaxSaleQty());
            
            if($qty > $maximumQty){
            
                $result['error'] = $this->__('Product Has Reached To Maximum Allowed Qty: %s', $maximumQty);
            $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            
            $product->setQty($qty);
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            
            $quote->collectTotals()
                    ->save();
                    $result['success'] = $this->__('Increased');
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
					
        }
        else if ($b=="dec" && $a){
     
            $qty = intval($product->getQty()-1);
            $minimumQty = intval(Mage::getModel('catalog/product')->load($product->getProductId())->getStockItem()->getMinSaleQty());
                        
            if($qty < $minimumQty){
                
                $result['error'] = $this->__('Product Has Reached To Minimal Allowed Qty: %s', $minimumQty);
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
                    
            if($qty > 0){
                    $product->setQty($qty);
            }else{
                $quote->removeItem($a);
               }
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                    
            $quote->collectTotals()
                ->save();
                  $result['success'] = $this->__('Decreased');
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
        }
    }
     public function couponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
       
        if ($this->_expireAjax()) {
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($remov=$this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
         //$result['enabled']=false;
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
           $result['enabled']=false;
            return;
        }
      

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($couponCode) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $result['success'] = $this->__('Coupon code "%s" was applied successfully.', Mage::helper('core')->htmlEscape($couponCode));
                    $result['reload']=true;
                }
                else {
                   
                   $result['error'] =$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                   $result['reload']=false;
                }
            } else {
                $result['error'] = $this->__('Coupon code was canceled successfully.');
               $result['reload']=true;
            }

        }
        catch (Mage_Core_Exception $e) {
             $result['error'] = $e->getMessage();
        }
        catch (Exception $e) {
           
            $result['error'] = $this->__('Can not apply coupon code.');
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
    }
    public function saveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        
        if ($this->getRequest()->isPost()) {
            $result = array();
            
            Mage::dispatchEvent('checkout_controller_quickcheckout_save_order_after', array(
                'request' => $this->getRequest(),
                'quote' => $this->getOnepage()->getQuote()
            ));
            if($heard=$this->getRequest()->getPost('quickcheckout_heard')){
              
                if($this->getRequest()->getPost('quickcheckout_other') && $heard=="Others"){
                    $heard=$this->getRequest()->getPost('quickcheckout_other');
                }
                $this->getOnepage()->getQuote()->setData('quickcheckout_heard', $heard);
            }
            if($comment=$this->getRequest()->getPost('quickcheckout_comment')){
               $this->getOnepage()->getQuote()->setData('quickcheckout_comment', nl2br(strip_tags($comment)));
            }
            //save BillingAddres
            $billingPostData   = $this->getRequest()->getPost('billing', array());
            if(Mage::getVersion() >= '1.4.0.1' && Mage::getVersion() < '1.4.2.0' ){
		 $billingData       = $this->_filterPostData($billingPostData);
            }else{
                //Not supported by some versions.
               
                $billingData       = $billingPostData;
            }
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            
            if (isset($billingData['email'])) {
                $billingData['email'] = trim($billingData['email']);
            }
             if ($this->getRequest()->getParam('is_subscribed', false)) {
                if(!$email=Mage::getSingleton('customer/session')->getCustomer()->getEmail()){
                    $email=$billingData['email'];
                }
                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                   $result['email']=($this->__('Confirmation request has been sent'));
                }
           
            }
            $resultBilling = $this->getOnepage()->saveBilling($billingData, $customerAddressId);
            if (isset($resultBilling['error'])) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = 'Billing Error: ' . $resultBilling['message'];
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            if (isset($billingData['use_for_shipping']) && $billingData['use_for_shipping'] == 1) {
                //save ShippingAddress
                $shippingData      = $this->getRequest()->getPost('shipping', array());
                $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
                $resultShipping    = $this->getOnepage()->saveShipping($shippingData, $customerAddressId);
                if (isset($resultShipping['error'])) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = 'Shipping Error: ' . $resultShipping['message'];
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
            } else {
                $resultShipping = $this->getOnepage()->saveShipping($billingData, $customerAddressId);
                if (isset($resultShipping['error'])) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = 'Shipping Error: ' . $resultShipping['message'];
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
            }
            //save Shipping Method
            $shippingMethodData   = $this->getRequest()->getPost('shipping_method', '');
            $resultShippingMethod = $this->getOnepage()->saveShippingMethod($shippingMethodData,'');

            try {
                //save Payment
                $paymentData   = $this->getRequest()->getPost('payment', array());
                $resultPayment = $this->getOnepage()->savePayment($paymentData);
               //$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                //     $this->getResponse()->setBody(Zend_Json::encode($redirectUrl));
                //return;
                

                if ($this->getRequest()->getPost('payment', false) && Mage::getSingleton('checkout/session')->getRBR()) {
                    $data= $this->getRequest()->getPost('payment', false);
                    $this->getOnepage()->getQuote()->getPayment()->importData($data);
                    $this->getOnepage()->saveOrder();
                    $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
                  }
       
                if (isset($resultPayment['error'])) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = 'Your order cannot be completed at this time as there is no payment methods available for it.';
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = 'Payment Method Error:' . $e->getMessage();
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            catch (Mage_Core_Exception $e) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = 'Core Exception: ' . $e->getMessage();
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = 'Exception: ' . $this->__('Unable to set Payment Method.');
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            
            try {
                if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                    $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                    if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                        $result['success'] = false;
                        $result['error'] = true;
                        $result['error_messages'] = $this->__('Please agree to all Terms and Conditions before placing the order.');
                        $this->getResponse()->setBody(Zend_Json::encode($result));
                        return;
                    }
                }
                if($this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl()){
                   
                    $redirectUrl       = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                    
                }
             
                $result['success'] = true;
                $result['error']   = false;
            }
            catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
                $this->getOnepage()->getQuote()->save();
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $e->getMessage();
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
                $this->getOnepage()->getQuote()->save();
                $result['success']  = false;
                $result['error']    = true;
                $result['error_messages'] = 'Exception: ' . $this->__('There was an error processing your order. Please contact us or try again later.');
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
            $this->getOnepage()->getQuote()->save();
            //$result['error'] = '123';
            $this->getCheckout()->unsetData('use_for_shipping');
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
    protected function getCheckout()
    {
        return $this->getOnepage()->getCheckout();
    }
    protected function _getReviewHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onestep_review');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
     protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onestep_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onestep_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    private function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    private function setDefaultCountryId()
    {
        $defaultCountry = Mage::getStoreConfig('general/country/default');
        $this->getQuote()->getShippingAddress()->setCountryId($defaultCountry)->save();
    }
}