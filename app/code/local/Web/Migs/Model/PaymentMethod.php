<?php

class Web_Migs_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'migs';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = false;
    protected $_canUseCheckout = true;
    //protected $_isInitializeNeeded = true;

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {      
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);
        $payment->authorize(true, $order->getBaseTotalDue()); // base amount will be set inside
        //$payment->setAmountAuthorized($order->getTotalDue());
        $order->setState(Mage_Sales_Model_Order::STATE_NEW, false, '', false);
        $stateObject->setState(Mage_Sales_Model_Order::STATE_NEW);
        $stateObject->setIsNotified(false); 
    }
    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $orderIncrementId = $payment->getOrder()->getIncrementId();
        $order = $payment->getOrder();
        $request = Mage::app()->getRequest();
        $SECURE_SECRET = $this->getConfigData('secure_secret'); //"3B65C63490BE17566728F1F478BF9010";
        $vpcURL = $this->getConfigData('cgi_url');
        $md5HashData = $SECURE_SECRET;

        $_data = array();
        $_data['Title'] = $this->getTitle();
        $_data['vpc_AccessCode'] = $this->getConfigData('access_code');
        $_data['vpc_Amount'] = 100 * $amount;
        $_data['vpc_Command'] = 'pay';
        $_data['vpc_Locale'] = 'en';
        $_data['vpc_MerchTxnRef'] = $orderIncrementId;
        $_data['vpc_Merchant'] = $this->getConfigData('merchant_no');
        $_data['vpc_OrderInfo'] = $orderIncrementId;
        $_data['vpc_ReturnURL'] = Mage::getUrl('migs/index');
        $_data['vpc_Version'] = '1';
        
        ksort($_data);

        $appendAmp = 0;
        foreach ($_data as $key => $value)
        {
            if (strlen($value) > 0)
            {
                if ($appendAmp == 0)
                {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else
                {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                $md5HashData .= $value;
            }
        }

        if (strlen($SECURE_SECRET) > 0)
        {
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
        }
        try
        {
            Mage::getSingleton('customer/session')->setRedirectUrl($vpcURL);
        }
        catch (exception $e)
        {
            Mage::throwException($e->getMessage());
        }

    }
    public function setOrderSuccessStatus($_orderId)
    {
        $successStatus = $this->getConfigData('order_status_success');
        if($successStatus){
            $order = Mage::getModel('sales/order')->loadByIncrementId($_orderId);
            $order->setStatus($successStatus)->save();
        }

    }
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getSingleton('customer/session')->getRedirectUrl();
    }

}
