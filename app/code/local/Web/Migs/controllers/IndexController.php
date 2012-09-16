<?php
class Web_Migs_IndexController extends Mage_Checkout_Controller_Action
{

    public function indexAction()
    {
        $request = $this->getRequest();
        $_data = $request->getParams();
        $session = Mage::getSingleton('checkout/session');

        $_orderId = $request->getParam('vpc_OrderInfo');
        if(!$_orderId){
            $this->__failure();
            return;
        }
        $SECURE_SECRET = Mage::getStoreConfig('secure_secret');
        $vpc_Txn_Secure_Hash = $request->getParam('vpc_SecureHash');
        
        $errorExists = false;
        if (strlen($SECURE_SECRET) > 0 
        && $request->getParam("vpc_TxnResponseCode") != "7" 
        && $request->getParam("vpc_TxnResponseCode") != "No Value Returned")
        {
            
            $md5HashData = $SECURE_SECRET;
            foreach ($_data as $key => $value)
            {
                if ($key != "vpc_SecureHash" or strlen($value) > 0)
                {
                    $md5HashData .= $value;
                }
            }

            if (strtoupper($vpc_Txn_Secure_Hash) != strtoupper(md5($md5HashData)))
            {
                $errorExists = true;
            }
        } else
        {
            $errorExists = true;
        }

        $rcode = $request->getParam("vpc_TxnResponseCode");
        $rstatus = $request->getParam("vpc_AcqResponseCode");
        $txnResponseCode = Mage::helper('migs')->null2unknown(false,$rcode);
        $acqResponseCode = Mage::helper('migs')->null2unknown(false,$rstatus);

        if ($errorExists === false && $txnResponseCode == '0' && $_orderId)
        {
            try
            {
                $order = Mage::getModel('sales/order')->loadByIncrementId($_orderId);
                $order->sendNewOrderEmail();
            }
            catch (exception $e)
            {
                Mage::throwException(Mage::helper('migs')->__('Can not send new order email.'));
            }
            Mage::getModel('migs/info')->setReceiptNo($request->getParam('vpc_ReceiptNo'))
                ->setOrderId($_orderId)
                ->setMessage($request->getParam('vpc_Message'))
                ->setInfo(serialize($_data))
                ->setCcType($request->getParam('vpc_Card'))
                ->setCcLast4(str_ireplace('x','',$request->getParam('vpc_CardNum')))
                ->save();

            $this->__success();
        } else{
            
            if ($rcode == 'C')
            {
                Mage::helper('migs')->addItemsToCart($_orderId);
                $session->setErrorMessage(Mage::helper('migs')->getResponseDescription($rcode));
                
                Mage::getModel('migs/info')->setReceiptNo($request->getParam('vpc_ReceiptNo'))
                ->setOrderId($_orderId)
                ->setMessage($request->getParam('vpc_Message'))
                ->setInfo(serialize($_data))
                ->setCcType($request->getParam('vpc_Card'))
                ->setCcLast4(str_ireplace('x','',$request->getParam('vpc_CardNum')))        
                ->save();

                $this->__failure();
            } else
            {
                Mage::helper('migs')->addItemsToCart($_orderId);
                
                Mage::getModel('migs/info')->setReceiptNo($request->getParam('vpc_ReceiptNo'))
                ->setOrderId($_orderId)
                ->setMessage($request->getParam('vpc_Message'))
                ->setInfo(serialize($_data))
                ->setCcType($request->getParam('vpc_Card'))
                ->setCcLast4(str_ireplace('x','',$request->getParam('vpc_CardNum')))        
                ->save();
                
                $session->setErrorMessage(Mage::helper('migs')->getResponseDescription($rcode));
                $this->__failure();
            }
        }            
        $this->renderLayout();
    }

    private function __success()
    {

        $this->_redirect('checkout/onepage/success');
    }
    private function __failure()
    {
        $this->_redirect('checkout/onepage/failure');
    }
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
}
