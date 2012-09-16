<?php

class Web_Migs_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkOrderPayment($orderId)
    {
        //$orderId = 100004173; 
        $vpcURL = Mage::getSingleton('migs/paymentMethod')->getConfigData('api_url');
        $postData = 'vpc_Version=1&vpc_Command=queryDR&vpc_AccessCode='.
        Mage::getSingleton('migs/paymentMethod')->getConfigData('access_code').'&vpc_Merchant='.
        Mage::getSingleton('migs/paymentMethod')->getConfigData('merchant_no').'&vpc_MerchTxnRef='.
        $orderId.'&vpc_User='.
        Mage::getSingleton('migs/paymentMethod')->getConfigData('api_username').'&vpc_Password='
        .Mage::getSingleton('migs/paymentMethod')->getConfigData('api_password');

        $return = array();
        ob_start();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $vpcURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($ch);
        $response = ob_get_contents();
        ob_end_clean();
        $message = "";
        // serach if $response contains html error code
        if (strchr($response, "<html>") || strchr($response, "<html>"))
        {
            ;
            $message = $response;
        } else
        {
            // check for errors from curl
            if (curl_error($ch))
            {
                $message = "%s: s" . curl_errno($ch) . "<br/>" . curl_error($ch);
                Mage::log($message, null, 'MigsCURL.txt');
                $return['result'] = false;
                $return['message'] = $message;
                return $return;
            }
        }
        curl_close($ch);

        $map = array();
        // process response if no errors
        if (strlen($message) == 0)
        {
            $pairArray = explode("&", $response);
            foreach ($pairArray as $pair)
            {
                $param = explode("=", $pair);
                $map[urldecode($param[0])] = urldecode($param[1]);
            }
            $message = self::null2unknown($map, "vpc_Message");
        }
        $amount = self::null2unknown($map, "vpc_Amount");
        $locale = self::null2unknown($map, "vpc_Locale");
        $batchNo = self::null2unknown($map, "vpc_BatchNo");
        $command = self::null2unknown($map, "vpc_Command");
        $version = self::null2unknown($map, "vpc_Version");
        $cardType = self::null2unknown($map, "vpc_Card");
        $cardNum = self::null2unknown($map, "vpc_CardNum");
        $orderInfo = self::null2unknown($map, "vpc_OrderInfo");
        $receiptNo = self::null2unknown($map, "vpc_ReceiptNo");
        $merchantID = self::null2unknown($map, "vpc_Merchant");
        $authorizeID = self::null2unknown($map, "vpc_AuthorizeId");
        $transactionNo = self::null2unknown($map, "vpc_TransactionNo");
        $acqResponseCode = self::null2unknown($map, "vpc_AcqResponseCode");
        $txnResponseCode = self::null2unknown($map, "vpc_TxnResponseCode");

        // QueryDR Data
        $drExists = self::null2unknown($map, "vpc_DRExists");
        $multipleDRs = self::null2unknown($map, "vpc_FoundMultipleDRs");

        // 3-D Secure Data
        $verType = self::null2unknown($map, "vpc_VerType");
        $verStatus = self::null2unknown($map, "vpc_VerStatus");
        $token = self::null2unknown($map, "vpc_VerToken");
        $verSecurLevel = self::null2unknown($map, "vpc_VerSecurityLevel");
        $enrolled = self::null2unknown($map, "vpc_3DSenrolled");
        $xid = self::null2unknown($map, "vpc_3DSXID");
        $acqECI = self::null2unknown($map, "vpc_3DSECI");
        $authStatus = self::null2unknown($map, "vpc_3DSstatus");

        // AMA Transaction Data
        $shopTransNo = self::null2unknown($map, "vpc_ShopTransactionNo");
        $authorisedAmount = self::null2unknown($map, "vpc_AuthorisedAmount");
        $capturedAmount = self::null2unknown($map, "vpc_CapturedAmount");
        $refundedAmount = self::null2unknown($map, "vpc_RefundedAmount");
        $ticketNumber = self::null2unknown($map, "vpc_TicketNo");


        // Define an AMA transaction output for Refund & Capture transactions
        $amaTransaction = true;
        if ($shopTransNo == "No Value Returned")
        {
            $amaTransaction = false;
        }
        //print_r($drExists);
        $return['message'] = 'Status Update :'.
            '<br>ReceiptNo : '.$receiptNo.
            '<br>Amount : ' .($amount/100).
            '<br>Card : '.$cardType.$cardNum.
            '<br>Message : '.$message;
        if ($txnResponseCode != "7" && $txnResponseCode != "No Value Returned" &&
            strtoupper($drExists) == "Y" && $message == 'Approved')
        {
            $return['result'] = true;
        } else
        {
            $return['result'] = true;
        }
        return $return;
    }
    public function addItemsToCart($_orderId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($_orderId);
        if(!$order){
            return;
        }
        $addToCart = Mage::getSingleton('migs/paymentMethod')->getConfigData('add_items_cancel');
        $cancelOrders = Mage::getSingleton('migs/paymentMethod')->getConfigData('cancel_fail');
        
        if($cancelOrders){
            $order->cancel()->save();
        }
        if($addToCart){
            Mage::getSingleton('checkout/session')->clear();
            $cart = Mage::getSingleton('checkout/cart');
            $items = $order->getItemsCollection();
            foreach ($items as $item)
            {
                try
                {
                    $cart->addOrderItem($item);
                }
                catch (Mage_Core_Exception $e)
                {
                    if (Mage::getSingleton('checkout/session')->getUseNotice(true))
                    {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    } else
                    {
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    }
                    $this->_redirect('*/*/history');
                }
                catch (exception $e)
                {
                    Mage::getSingleton('checkout/session')->addException($e, Mage::helper('checkout')->
                        __('Cannot add the item to shopping cart.'));
                    $this->_redirect('checkout/cart');
                }
            }
            $cart->save();
        }
        
    }
    public static function null2unknown($map = false,$code)
    {
        if(!$map){
            if ($code == "" )
            {
                return "No Value Returned";
            } else
            {
                return $code;
            }
        }
        if ($code == "" || !isset($map[$code]))
        {
            return "No Value Returned";
        } else
        {
            return $map[$code];
        }
    }
    public static function getResponseDescription($responseCode)
    {
        switch ($responseCode)
        {
            case "0":
                $result = "Transaction Successful";
                break;
            case "?":
                $result = "Transaction status is unknown";
                break;
            case "1":
                $result = "Unknown Error";
                break;
            case "2":
                $result = "Bank Declined Transaction";
                break;
            case "3":
                $result = "No Reply from Bank";
                break;
            case "4":
                $result = "Expired Card";
                break;
            case "5":
                $result = "Insufficient funds";
                break;
            case "6":
                $result = "Error Communicating with Bank";
                break;
            case "7":
                $result = "Payment Server System Error";
                break;
            case "8":
                $result = "Transaction Type Not Supported";
                break;
            case "9":
                $result = "Bank declined transaction (Do not contact Bank)";
                break;
            case "A":
                $result = "Transaction Aborted";
                break;
            case "C":
                $result = "Transaction Cancelled";
                break;
            case "D":
                $result = "Deferred transaction has been received and is awaiting processing";
                break;
            case "F":
                $result = "3D Secure Authentication failed";
                break;
            case "I":
                $result = "Card Security Code verification failed";
                break;
            case "L":
                $result = "Shopping Transaction Locked (Please try the transaction again later)";
                break;
            case "N":
                $result = "Cardholder is not enrolled in Authentication scheme";
                break;
            case "P":
                $result = "Transaction has been received by the Payment Adaptor and is being processed";
                break;
            case "R":
                $result = "Transaction was not processed - Reached limit of retry attempts allowed";
                break;
            case "S":
                $result = "Duplicate SessionID (OrderInfo)";
                break;
            case "T":
                $result = "Address Verification Failed";
                break;
            case "U":
                $result = "Card Security Code Failed";
                break;
            case "V":
                $result = "Address Verification and Card Security Code Failed";
                break;
            default:
                $result = "Unable to be determined";
        }
        return $result;
    }
    public static function getStatusDescription($statusResponse)
    {
        if ($statusResponse == "" || $statusResponse == "No Value Returned")
        {
            $result = "3DS not supported or there was no 3DS data provided";
        } else
        {
            switch ($statusResponse)
            {
                case "Y":
                    $result = "The cardholder was successfully authenticated.";
                    break;
                case "E":
                    $result = "The cardholder is not enrolled.";
                    break;
                case "N":
                    $result = "The cardholder was not verified.";
                    break;
                case "U":
                    $result = "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer.";
                    break;
                case "F":
                    $result = "There was an error in the format of the request from the merchant.";
                    break;
                case "A":
                    $result = "Authentication of your Merchant ID and Password to the ACS Directory Failed.";
                    break;
                case "D":
                    $result = "Error communicating with the Directory Server.";
                    break;
                case "C":
                    $result = "The card type is not supported for authentication.";
                    break;
                case "S":
                    $result = "The signature on the response received from the Issuer could not be validated.";
                    break;
                case "P":
                    $result = "Error parsing input from Issuer.";
                    break;
                case "I":
                    $result = "Internal Payment Server system error.";
                    break;
                default:
                    $result = "Unable to be determined";
                    break;
            }
        }
        return $result;
    }
   	public function unpackage($queryString) {
		$array = array();
		parse_str($queryString, $array);

		return $array;
	}

}
