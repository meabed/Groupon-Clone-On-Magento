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

class ND_MigsVpc_Model_Merchant extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'migsvpc_merchant';
    
    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment        = true;

    protected $_formBlockType = 'migsvpc/merchant_form';
    protected $_paymentMethod = 'merchant';
    protected $_infoBlockType = 'migsvpc/payment_info';

    protected $_order;
    protected $_quote;
    
    /**
     * Payment actions
     * @var string
     */
    const ACTION_AUTHORIZE  = 'authorize';
    const ACTION_AUTHORIZE_CAPTURE = 'authorize_capture';
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
     * Order increment ID getter (either real from order or a reserved from quote)
     *
     * @return string
     */
    private function _getOrderId()
    {
        $info = $this->getInfoInstance();

        if ($this->_isPlaceOrder()) {
            return $info->getOrder()->getIncrementId();
        } else {
            if (!$info->getQuote()->getReservedOrderId()) {
                $info->getQuote()->reserveOrderId();
            }
            return $info->getQuote()->getReservedOrderId();
        }
    }

    public function _checkout()
    {
        return Mage::getModel('core/session');
    }
    /**
     * Grand total getter
     *
     * @return string
     */
    private function _getAmount()
    {
        if(Mage::getStoreConfig('payment/' . $this->getCode() . '/test_gateway'))
        {
            return 1000;
        }
        else
        {
            $info = $this->getInfoInstance();
            if ($this->_isPlaceOrder()) {
                $_amount = (double)$info->getOrder()->getQuoteBaseGrandTotal()*100;
            } else {
                $_amount = (double)$info->getQuote()->getBaseGrandTotal()*100;
            }
            return $_amount; 
        }
    }
    
    /**
     * Whether current operation is order placement
     *
     * @return bool
     */
    private function _isPlaceOrder()
    {
        $info = $this->getInfoInstance();
        if ($info instanceof Mage_Sales_Model_Quote_Payment) {
            return false;
        } elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
            return true;
        }
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

    /**
     * Get currency that accepted by MIGS account
     *
     * @return string
     */
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
    
    /*public function validate()
    {        
        parent::validate();        
    }*/    
    
    public function callDoSecurePayment(Varien_Object $payment, $amount)
    {   
        $paymentInfo = $this->getInfoInstance();
        $this->_quote = Mage::getModel('sales/quote')->load($paymentInfo->getQuoteId());
        // Parameters of paymentInfo object
        /*{"payment_id":"55","quote_id":"32","created_at":"2011-08-11 06:04:20","updated_at":"2011-08-11 10:51:39","method":"migsvpc_merchant","cc_type":"MC","cc_number_enc":"","cc_last4":"3430","cc_cid_enc":"","cc_owner":null,"cc_exp_month":"5","cc_exp_year":"2013","cc_ss_owner":"","cc_ss_start_month":null,"cc_ss_start_year":null,"cybersource_token":"","paypal_correlation_id":"","paypal_payer_id":"","paypal_payer_status":"","po_number":"","additional_data":null,"cc_ss_issue":null,"additional_information":[],"ideal_issuer_id":null,"ideal_issuer_list":null,"method_instance":{},"cc_number":"5313581000123430","cc_cid":"123"}*/
        
        //Mage::throwException(Mage::helper('core')->jsonEncode($this->getQuote()));
        $card_types = array('VI'=>'Visa','MC'=>"Mastercard");
        $cc_month = date("m",strtotime($paymentInfo->getCcExpYear().'-'.$paymentInfo->getCcExpMonth()));
        $cc_year = date("y",strtotime($paymentInfo->getCcExpYear().'-'.$paymentInfo->getCcExpMonth()));
        $cc_date = $cc_year.$cc_month;
        
        
        
        $fields = array(
                "vpc_AccessCode"=>$this->getAccessCode(),
                "vpc_Amount"=>$this->_getAmount(), // 1000 - For Testing
                "vpc_card"=>$card_types[$paymentInfo->getCcType()],
                //"vpc_CardExp"=>$cc_date,
                //"vpc_CardNum"=>$paymentInfo->getCcNumber(),
                "vpc_CardSecurityCode"=>$paymentInfo->getCcCid(),                
                "vpc_Command"=>"pay",
                //"vpc_gateway"=>"threeDSecure",
                "vpc_Locale"=>"en",
                "vpc_MerchTxnRef"=>$this->_getOrderId(),                    
                "vpc_Merchant"=>$this->getMerchantId(),                    
                "vpc_OrderInfo"=>$this->_getOrderId(),                                                
                "vpc_ReturnURL"=>Mage::getUrl('migsvpc/' . $this->_paymentMethod . '/response', array('_secure' => true)),
                //"vpc_TicketNo"=>1,
                "vpc_Version"=>1,
                //"vpc_SecureHash"=>$secure_hash_key
                );
                
                $postRequestData = '';
                $amp = '';
                $md5HashData = '';
                foreach($fields as $key => $val)                
                {
                    $postRequestData .= $amp . urlencode($key) . '=' . urlencode($val);
                    $amp = '&';
                    $md5HashData .= $val;
                }

                $secure_hash_key = strtoupper(md5($this->getSecureHashKey().$md5HashData));
                
                $fields["vpc_SecureHash"]=$secure_hash_key;
                $postRequestData .= '&vpc_SecureHash='.$secure_hash_key;
                
                //echo "<pre>"; print_r($postRequestData); die;
                Mage::log('MIGS Request: '.$postRequestData, null, 'migs_payment.log');
                
                ob_start();
                $clientURL = curl_init();
                // Initialise the client url variables
                // REFER to curl_setopt for more options as suits your requirements
                // such as Verify SSL, Proxy etc
                $vpcURL = $this->getMigsVpcMerchantUrl();
                curl_setopt ($clientURL, CURLOPT_URL, $vpcURL);
                curl_setopt ($clientURL, CURLOPT_POST, 1);
                curl_setopt ($clientURL, CURLOPT_POSTFIELDS, $postRequestData);
                curl_exec ($clientURL); // Open connection
                $vpcResponse = ob_get_contents(); // Get result                
                ob_end_clean(); // Finish with the buffer
                // Check for errors
                
                if(strchr($vpcResponse,"<html>")) $errorMessage = $vpcResponse;
                else if(curl_error($clientURL)) $errorMessage = "CURL ERROR: " . curl_errno($clientURL) . " " . curl_error($clientURL);
                // Communication Issues should be sent to Administrator, not to screen
                curl_close($clientURL); // Close the connection                
                $responseKeyVals = explode("&", $vpcResponse);
                foreach ($responseKeyVals as $val) {
                    $param = explode("=", $val);
                    if(count($param)>0) 
                    {
                        $responseArray[urldecode($param[0])] = urldecode($param[1]);
                    }
                }
                Mage::log('MIGS Response: '.$vpcResponse, null, 'migs_payment.log');
                //Mage::throwException(Mage::helper('core')->jsonEncode($vpcURL.'?'.$postRequestData));
                
                if($responseArray['vpc_AcqResponseCode']=='00' && $responseArray['vpc_TxnResponseCode']=='0')
                {
                    //Mage::throwException(Mage::helper('core')->__('Success'));
                    //Mage::throwException(Mage::helper('core')->jsonEncode($this->_quote->getPayment()));
                        $paymentInfo->setAdditionalInformation(ND_MigsVpc_Model_Info::AUTHORIZE_ID,$responseArray['vpc_AuthorizeId'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::ORDER_INFO,$responseArray['vpc_OrderInfo'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::RECEIPT_NO,$responseArray['vpc_ReceiptNo'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::TRANSACTION_NO,$responseArray['vpc_TransactionNo'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::MERCH_TXN_REF,$responseArray['vpc_MerchTxnRef'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::BATCH_NO,$responseArray['vpc_BatchNo'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::AVS_RESULT_CODE,$responseArray['vpc_AVSResultCode'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::AVS_RESPONSE_CODE,$responseArray['vpc_AcqAVSRespCode'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::ACQ_CSC_RESPONSE_CODE,$responseArray['vpc_AcqCSCRespCode'])
                                    ->setAdditionalInformation(ND_MigsVpc_Model_Info::RISK_OVERALL_RESULT,$responseArray['vpc_RiskOverallResult'])
                                    ->setLastTransId($responseArray['vpc_TransactionNo']) // vpc_TransactionNo
                                    ;
                        //$this->_quote->getPayment()->save();
                                        
                    return $responseArray;
                }
                elseif($responseArray['vpc_AcqResponseCode']!='' || $responseArray['vpc_TxnResponseCode']!='')
                {
                    Mage::throwException($responseArray['vpc_Message']);
                }
                else
                {
                    Mage::throwException(Mage::helper('core')->__('This card has failed validation and cannot be used.'));
                }
    }
    
    /*public function prepareSave()
    {
        //return parent::_beforeSave();   
    }
    
    public function assignData($data)
    {
        //return parent::assignData($data);        
    }*/

    /**
     * Get url of MIGS Merchant Hosted Payment
     *
     * @return string
     */
    public function getMigsVpcMerchantUrl()
    {
         if (!$url = Mage::getStoreConfig('payment/' . $this->getCode() . '/api_url')) {
             $url = 'https://migs.mastercard.com.au/vpcdps';
         }
         return $url;
    }
    
    public function getOrderPlaceRedirectUrl()
    {
        /*$url = Mage::getUrl('migsvpc/' . $this->_paymentMethod . '/redirect');
        if(!$url) {
            $url = 'https://migs.mastercard.com.au/vpcdps';
        }
        return $url;*/
        return;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    /*public function parseResponse()
    {
        $response = $this->getResponse();

        if ($response['vpc_TrxnStatus'] == 'True') {
            return true;
        }
        return false;
    }*/
    
    /**
     * Get debug flag
     *
     * @return string
     */
    /*public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }*/
    
    public function getConfigPaymentAction()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/payment_action');
    }
    
    /*protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $payment->setTransactionId($this->_checkout()->getTransactionId());
    }*/
    
    /**
     * Authorize payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $transaction_id = Mage::getModel('checkout/session')->getQuote()->getPayment()->getVpcTransactionNo();
        Mage::throwException('a'.$transaction_id);
        //$order = $payment->getOrder();
        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        //$paymentInfo = $this->getInfoInstance();
        $result = $this->callDoSecurePayment();
        //$paymentAdditionalInfo = Mage::getModel('checkout/session')->getQuote()->getPayment()->getAdditionalInformation();        
        //$quote_id = Mage::getModel('checkout/session')->getQuote()->getId();
        //$transaction_id = $paymentAdditionalInfo['vpc_transaction_no'];
        $transaction_id = $result['vpc_TransactionNo'];
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId())
                ->setTransactionId($transaction_id);
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

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }
}
