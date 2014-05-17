<?php 
/**
 * ND MigsVpc payment gateway
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * Original code copyright (c) 2008 Irubin Consulting Inc. DBA Varien
 *
 * @category ND
 * @package    ND_MigsVpc
 * @copyright  Copyright (c) 2010 ND MigsVpc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ND_MigsVpc_Model_Merchantnew extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'migsvpc_merchantnew';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'migsvpc/merchantnew_form';
    protected $_paymentMethod = 'merchantnew';
    protected $_infoBlockType = 'migsvpc/payment_info';

    protected $_order;
    
    const TYPE_SSL  = 'ssl';
    const TYPE_3DSECURE = 'threeDSecure';

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }
    
    /**
     * Grand total getter
     *
     * @return string
     */
    private function _getAmount()
    {
        /*if(Mage::getStoreConfig('payment/' . $this->getCode() . '/test_gateway'))
        {
            return 1000;
        }
        else
        {*/
            $_amount = (double)$this->getOrder()->getBaseGrandTotal();           
            return $_amount*100; 
        //}
    }

    /**
     * Get Customer Id
     *
     * @return string
     */
    public function getMerchantId()
    {
        if(Mage::getStoreConfig('payment/' . $this->getCode() . '/test_gateway'))
            $merchant_id = Mage::getStoreConfig('payment/' . $this->getCode() . '/merchant_id');
        else
            $merchant_id = Mage::getStoreConfig('payment/' . $this->getCode() . '/merchant_id');
            
        return $merchant_id;
    }

    public function getAccessCode()
    {
        if(Mage::getStoreConfig('payment/' . $this->getCode() . '/test_gateway'))
            $access_code = Mage::getStoreConfig('payment/' . $this->getCode() . '/access_code_test');
        else
            $access_code = Mage::getStoreConfig('payment/' . $this->getCode() . '/access_code');
        
        return $access_code;            
    }
    
    public function getSecureHashKey()
    {
        if(Mage::getStoreConfig('payment/' . $this->getCode() . '/test_gateway'))
            $secure_hash_key = Mage::getStoreConfig('payment/' . $this->getCode() . '/secure_hash_secret_test');
        else
            $secure_hash_key = Mage::getStoreConfig('payment/' . $this->getCode() . '/secure_hash_secret');
            
        return $secure_hash_key;
    }
    
    public function getPaymentType()
    {
        $payment_type = Mage::getStoreConfig('payment/' . $this->getCode() . '/payment_type');
            
        return $payment_type;
    }

    public function validate()
    {   
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }     
        //Mage::throwException(Mage::helper('core')->jsonEncode($paymentInfo));  
       // if ($currency_code != $this->getConfig()->getCurrency()) {
         //   Mage::throwException(Mage::helper('ebscreditcard')->__('Selected currency //code ('.$currency_code.') is not compatabile with SecureEbs'));
       // }
       if($paymentInfo->getCcType()!='') {
           $dataAry['cc_owner'] = $paymentInfo->getCcOwner();
           $dataAry['cc_type'] = $paymentInfo->getCcType();
           $dataAry['cc_number'] = $paymentInfo->getCcNumber();
           $dataAry['cc_exp_month'] = $paymentInfo->getCcExpMonth();
           $dataAry['cc_exp_year'] = $paymentInfo->getCcExpYear();
           $dataAry['cc_cid'] = $paymentInfo->getCcCid();
           $paymentData = serialize($dataAry);
           Mage::getSingleton('core/session')->setCCPaymentData($paymentData);
           //Mage::throwException(Mage::helper('core')->jsonEncode($paymentData));  
       }       
       return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        $url = Mage::getUrl('migsvpc/' . $this->_paymentMethod . '/redirect');
        if(!$url) {
            $url = 'https://migs.mastercard.com.au/vpcpay';
        }
        return $url;
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {        
        $fieldsArr = array();        
        $lengs = 0;        
        $paymentInfo = $this->getInfoInstance();
        $paymentData = unserialize(Mage::getSingleton('core/session')->getCCPaymentData());   
        //echo '<pre>';print_r($paymentData);die;
        $card_types = array('VI'=>'Visa','MC'=>"Mastercard");     
        $cc_month = date("m",strtotime($paymentData['cc_exp_year'].'-'.$paymentData['cc_exp_month']));
        $cc_year = date("y",strtotime($paymentData['cc_exp_year'].'-'.$paymentData['cc_exp_month']));
        $cc_date = $cc_year.$cc_month;
        $fields = array(
                    "vpc_AccessCode"=>$this->getAccessCode(),
                    "vpc_Amount"=>$this->_getAmount(), // 1000 - For Testing
                    "vpc_CardExp"=>$cc_date,
                    "vpc_CardNum"=>$paymentData['cc_number'],
                    "vpc_CardSecurityCode"=>$paymentData['cc_cid'],
                    "vpc_Command"=>"pay",
                    "vpc_IPAddress"=>$_SERVER['REMOTE_ADDR'],
                    "vpc_Locale"=>"en",
                    "vpc_MerchTxnRef"=>$paymentInfo->getOrder()->getRealOrderId(),                    
                    "vpc_Merchant"=>$this->getMerchantId(),                    
                    "vpc_OrderInfo"=>$paymentInfo->getOrder()->getRealOrderId(),                                                
                    "vpc_ReturnURL"=>Mage::getUrl('migsvpc/' . $this->_paymentMethod . '/response', array('_secure' => true)),
                    //"vpc_TicketNo"=>'1',
                    "vpc_Version"=>'1',
                    "vpc_card"=>$card_types[$paymentData['cc_type']],
                    "vpc_gateway"=>$this->getPaymentType()
                    );
        $str = $this->getSecureHashKey();
        $postRequestData = array();
        foreach($fields as $key => $val)                
        {
            $str .= $val;
            if($key=='vpc_CardSecurityCode' || $key=='vpc_CardExp') continue;
            if($key=='vpc_CardNum') $val = substr($val,0,6);
            $postRequestData[] = $key.':'.$val;
        }

        $secure_hash_key = strtoupper(md5($str));
        $fields['vpc_SecureHash'] = $secure_hash_key;        
        /*$fieldsArr['vpc_AccessCode'] = $this->getAccessCode();       
        $fieldsArr['vpc_Amount'] = '10';//$this->_getAmount();        
        $fieldsArr['vpc_CardExp'] = $cc_date;
        $fieldsArr['vpc_CardNum'] = $paymentData['cc_number'];
        $fieldsArr['vpc_CardSecurityCode'] = $paymentData['cc_cid'];
        $fieldsArr['vpc_Command'] = 'pay';
        $fieldsArr['vpc_Locale'] = 'en';
        $fieldsArr['vpc_MerchTxnRef'] = $paymentInfo->getOrder()->getRealOrderId();
        $fieldsArr['vpc_Merchant'] = $this->getMerchantId();                
        $fieldsArr['vpc_OrderInfo'] = $paymentInfo->getOrder()->getRealOrderId();        
        $fieldsArr['vpc_ReturnURL'] = Mage::getUrl('migsvpc/' . $this->_paymentMethod . '/response', array('_secure' => true));                
        //$fieldsArr['vpc_TicketNo'] = '1';        
        $fieldsArr['vpc_Version'] = '1';  
        $fieldsArr['vpc_card'] = $card_types[$paymentData['cc_type']];
        $fieldsArr['vpc_gateway'] = 'threeDSecure';      
        $fieldsArr['vpc_SecureHash'] = $secure_hash_key;*/        
        
        //return $fieldsArr;
        //Mage::log('MIGS Request: '.implode(",",$postRequestData), null, 'migs_payment.log');
        Mage::log("\n".'MIGS Request: '.implode(",",$postRequestData), null, 'migs_payment.log');
        return $fields;
    }

    /**
     * Get url of Migs Shared Payment
     *
     * @return string
     */
    public function getMigsVpcMerchantUrl()
    {
         if (!$url = Mage::getStoreConfig('payment/migsvpc_merchantnew/api_url')) {
             $url = 'https://migs.mastercard.com.au/vpcpay';
         }
         return $url;
    }

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    public function assignData($data)
    {
        //Mage::throwException(implode(',',$data));
        $result = parent::assignData($data);        
        if (is_array($data)) {
            $this->getInfoInstance()->setAdditionalInformation($key, isset($data[$key]) ? $data[$key] : null);
        }
        elseif ($data instanceof Varien_Object) {
            $this->getInfoInstance()->setAdditionalInformation($key, $data->getData($key));
        }
        return $result;
    }
    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }
    
    public function afterSuccessOrder($response)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['vpc_OrderInfo']);
        //$paymentInst = $order->getPayment()->getMethodInstance();
		//$paymentInst = $order->getPayment();
        //$paymentInst->setTransactionId($response['vpc_TransactionNo']); 
        //$paymentInst->setStatus(self::STATUS_APPROVED)
                //->setLastTransId($response['vpc_TransactionNo'])
                //->setTransactionId($response['vpc_TransactionNo'])
                //->setAdditionalInformation(ND_MigsVpc_Model_Info::THREED_SENROLLED,$response['vpc_3DSenrolled']);
                /*->setAdditionalInformation(ND_MigsVpc_Model_Info::ORDER_INFO,$response['vpc_OrderInfo'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::RECEIPT_NO,$response['vpc_ReceiptNo'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::TRANSACTION_NO,$response['vpc_TransactionNo'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::MERCH_TXN_REF,$response['vpc_MerchTxnRef'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::BATCH_NO,$response['vpc_BatchNo'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::AVS_RESULT_CODE,$response['vpc_AVSResultCode'])
                ->RamboJewsetAdditionalInformation(ND_MigsVpc_Model_Info::AVS_RESPONSE_CODE,$response['vpc_AcqAVSRespCode'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::ACQ_CSC_RESPONSE_CODE,$response['vpc_AcqCSCRespCode'])
                ->setAdditionalInformation(ND_MigsVpc_Model_Info::RISK_OVERALL_RESULT,$response['vpc_RiskOverallResult'])*/
        //$paymentInst->save();
        $order->sendNewOrderEmail();
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $transaction->setTxnId($response['vpc_TransactionNo']);
		$order->getPayment()->setAdditionalInformation(ND_MigsVpc_Model_Info::THREED_SENROLLED,$response['vpc_3DSenrolled']);
        $transaction->setOrderPaymentObject($order->getPayment())					
                    ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
        $transaction->save();
        $order_status = Mage::helper('core')->__('Payment is successful.');
    
        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $order_status);
        $order->save();        
    }
}
