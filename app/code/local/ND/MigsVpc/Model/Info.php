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
class ND_MigsVpc_Model_Info
{
    /**
     * Cross-models public exchange keys
     *
     * @var string
     */
    const AUTHORIZE_ID          = 'vpc_authorize_id';
    const ORDER_INFO            = 'vpc_order_info';    
    const RECEIPT_NO            = 'vpc_receipt_no';
    const TRANSACTION_NO        = 'vpc_transaction_no';
    const MERCH_TXN_REF         = 'vpc_merch_txn_ref';
    const BATCH_NO              = 'vpc_batch_no';
    const AVS_RESULT_CODE       = 'vpc_avs_result_code';
    const AVS_RESPONSE_CODE     = 'vpc_avs_response_code';
    const ACQ_CSC_RESPONSE_CODE = 'vpc_acq_avs_response_code';
    const RISK_OVERALL_RESULT   = 'vpc_risk_overall_result';
    const THREED_SENROLLED      = 'vpc_threedsenrolled';

    /**
     * All payment information map
     *
     * @var array
     */
    protected $_paymentMap = array(
        self::AUTHORIZE_ID          => 'vpc_authorize_id',
        self::ORDER_INFO            => 'vpc_order_info',
        self::RECEIPT_NO            => 'vpc_receipt_no',
        self::TRANSACTION_NO        => 'vpc_transaction_no',
        self::MERCH_TXN_REF         => 'vpc_merch_txn_ref',
        self::BATCH_NO              => 'vpc_batch_no',
        self::AVS_RESULT_CODE       => 'vpc_avs_result_code',
        self::AVS_RESPONSE_CODE     => 'vpc_avs_response_code',
        self::ACQ_CSC_RESPONSE_CODE => 'vpc_acq_avs_response_code',
        self::RISK_OVERALL_RESULT   => 'vpc_risk_overall_result',
        self::THREED_SENROLLED      => 'vpc_threedsenrolled',
    );
    
    /**
     * MigsVpc payment status possible values
     *
     * @var string
     */
    const PAYMENTSTATUS_NONE         = 'none';
    const PAYMENTSTATUS_ACCEPTED     = 'accepted';
    const PAYMENTSTATUS_REJECTED     = 'rejected';
    const PAYMENTSTATUS_REVIEWED     = 'reviewed';
    const PAYMENTSTATUS_NOTCHECKED   = 'not_checked';
    const PAYMENTSTATUS_SYSREJECT    = 'system_rejected';

    /**
     * Map of payment information available to customer
     *
     * @var array
     */
    protected $_paymentPublicMap = array(
        //'',
    );

    /**
     * Rendered payment map cache
     *
     * @var array
     */
    protected $_paymentMapFull = array();

    /**
     * All available payment info getter
     *
     * @param Mage_Payment_Model_Info $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        // collect migsvpc-specific info
        $result = $this->_getFullInfo(array_values($this->_paymentMap), $payment, $labelValuesOnly);

        // add last_trans_id
        $label = Mage::helper('payment')->__('Last Transaction ID');
        $value = $payment->getLastTransId();
        if ($labelValuesOnly) {
            $result[$label] = $value;
        } else {
            $result['last_trans_id'] = array('label' => $label, 'value' => $value);
        }

        return $result;
    }

    /**
     * Public payment info getter
     *
     * @param Mage_Payment_Model_Info $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPublicPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        return $this->_getFullInfo($this->_paymentPublicMap, $payment, $labelValuesOnly);
    }

    /**
     * Grab data from source and map it into payment
     *
     * @param array|Varien_Object|callback $from
     * @param Mage_Payment_Model_Info $payment
     */
    public function importToPayment($from, Mage_Payment_Model_Info $payment)
    {
        $fullMap = array_merge($this->_paymentMap, $this->_systemMap);
        if (is_object($from)) {
            $from = array($from, 'getDataUsingMethod');
        }
        Varien_Object_Mapper::accumulateByMap($from, array($payment, 'setAdditionalInformation'), $fullMap);
    }

    /**
     * Grab data from payment and map it into target
     *
     * @param Mage_Payment_Model_Info $payment
     * @param array|Varien_Object|callback $to
     * @param array $map
     * @return array|Varien_Object
     */
    public function &exportFromPayment(Mage_Payment_Model_Info $payment, $to, array $map = null)
    {
        $fullMap = array_merge($this->_paymentMap, $this->_systemMap);
        Varien_Object_Mapper::accumulateByMap(array($payment, 'getAdditionalInformation'), $to,
            $map ? $map : array_flip($fullMap)
        );
        return $to;
    }

    /**
     * Check whether the payment is in review state
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    public static function isPaymentReviewRequired(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        if (self::PAYMENTSTATUS_PENDING === $paymentStatus) {
            $pendingReason = $payment->getAdditionalInformation(self::PENDING_REASON_GLOBAL);
            return !in_array($pendingReason, array('authorization', 'order'));
        }
        return false;
    }

    /**
     * Check whether fraud order review detected and can be reviewed
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    public static function isFraudReviewAllowed(Mage_Payment_Model_Info $payment)
    {
        return self::isPaymentReviewRequired($payment)
            && 1 == $payment->getAdditionalInformation(self::IS_FRAUD_GLOBAL);
    }

    /**
     * Check whether the payment is completed
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    public static function isPaymentCompleted(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        return self::PAYMENTSTATUS_COMPLETED === $paymentStatus;
    }

    /**
     * Check whether the payment was processed successfully
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    public static function isPaymentSuccessful(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        if (in_array($paymentStatus, array(
            self::PAYMENTSTATUS_COMPLETED, self::PAYMENTSTATUS_INPROGRESS, self::PAYMENTSTATUS_REFUNDED,
            self::PAYMENTSTATUS_REFUNDEDPART, self::PAYMENTSTATUS_UNREVERSED, self::PAYMENTSTATUS_PROCESSED,
        ))) {
            return true;
        }
        $pendingReason = $payment->getAdditionalInformation(self::PENDING_REASON_GLOBAL);
        return self::PAYMENTSTATUS_PENDING === $paymentStatus
            && in_array($pendingReason, array('authorization', 'order'));
    }

    /**
     * Check whether the payment was processed unsuccessfully or failed
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    public static function isPaymentFailed(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        return in_array($paymentStatus, array(
            self::PAYMENTSTATUS_REJECTED
        ));
    }

    /**
     * Explain pending payment reason code
     *
     * @param string $code
     * @return string
     */
    public static function explainPendingReason($code)
    {
        switch ($code) {
            case 'address':
                return Mage::helper('payment')->__('Customer did not include a confirmed address.');
            case 'authorization':
            case 'order':
                return Mage::helper('payment')->__('The payment is authorized but not settled.');
            case 'echeck':
                return Mage::helper('payment')->__('The payment eCheck is not yet cleared.');
            case 'intl':
                return Mage::helper('payment')->__('Merchant holds a non-U.S. account and does not have a withdrawal mechanism.');
            case 'multi-currency': // break is intentionally omitted
            case 'multi_currency': // break is intentionally omitted
            case 'multicurrency':
                return Mage::helper('payment')->__('The payment curency does not match any of the merchant\'s balances currency.');
            case 'paymentreview':
                return Mage::helper('payment')->__('The payment is pending while it is being reviewed by Migs for risk.');
            case 'unilateral':
                return Mage::helper('payment')->__('The payment is pending because it was made to an email address that is not yet registered or confirmed.');
            case 'verify':
                return Mage::helper('payment')->__('The merchant account is not yet verified.');
            case 'upgrade':
                return Mage::helper('payment')->__('The payment was made via credit card. In order to receive funds merchant must upgrade account to Business or Premier status.');
            case 'none': // break is intentionally omitted
            case 'other': // break is intentionally omitted
            default:
                return Mage::helper('payment')->__('Unknown reason. Please contact Migs customer service.');
        }
    }

    /**
     * Explain the refund or chargeback reason code
     *
     * @param $code
     * @return string
     */
    public static function explainReasonCode($code)
    {
        switch ($code) {
            case 'chargeback':
                return Mage::helper('payment')->__('Chargeback by customer.');
            case 'guarantee':
                return Mage::helper('payment')->__('Customer triggered a money-back guarantee.');
            case 'buyer-complaint':
                return Mage::helper('payment')->__('Customer complaint.');
            case 'refund':
                return Mage::helper('payment')->__('Refund issued by merchant.');
            case 'adjustment_reversal':
                return Mage::helper('payment')->__('Reversal of an adjustment.');
            case 'chargeback_reimbursement':
                return Mage::helper('payment')->__('Reimbursement for a chargeback.');
            case 'chargeback_settlement':
                return Mage::helper('payment')->__('Settlement of a chargeback.');
            case 'none': // break is intentionally omitted
            case 'other':
            default:
                return Mage::helper('payment')->__('Unknown reason. Please contact Migs customer service.');
        }
    }

    /**
     * Whether a reversal/refund can be disputed with Migs
     *
     * @param string $code
     * @return bool;
     */
    public static function isReversalDisputable($code)
    {
        switch ($code) {
            case 'none':
            case 'other':
            case 'chargeback':
            case 'buyer-complaint':
            case 'adjustment_reversal':
                return true;
            case 'guarantee':
            case 'refund':
            case 'chargeback_reimbursement':
            case 'chargeback_settlement':
            default:
                return false;
        }
    }

    /**
     * Render info item
     *
     * @param array $keys
     * @param Mage_Payment_Model_Info $payment
     * @param bool $labelValuesOnly
     */
    protected function _getFullInfo(array $keys, Mage_Payment_Model_Info $payment, $labelValuesOnly)
    {
        $result = array();
        foreach ($keys as $key) {
            if (!isset($this->_paymentMapFull[$key])) {
                $this->_paymentMapFull[$key] = array();
            }
            if (!isset($this->_paymentMapFull[$key]['label'])) {
                if (!$payment->hasAdditionalInformation($key)) {
                    $this->_paymentMapFull[$key]['label'] = false;
                    $this->_paymentMapFull[$key]['value'] = false;
                } else {
                    $value = $payment->getAdditionalInformation($key);
                    $this->_paymentMapFull[$key]['label'] = $this->_getLabel($key);
                    $this->_paymentMapFull[$key]['value'] = $value;
                    //$this->_paymentMapFull[$key]['value'] = $this->_getValue($value, $key);
                }
            }
            if (!empty($this->_paymentMapFull[$key]['value'])) {
                if ($labelValuesOnly) {
                    $result[$this->_paymentMapFull[$key]['label']] = $this->_paymentMapFull[$key]['value'];
                } else {
                    $result[$key] = $this->_paymentMapFull[$key];
                }
            }
        }
        return $result;
    }

    /**
     * Render info item labels
     *
     * @param string $key
     */
    protected function _getLabel($key)
    {
        switch ($key) {
            case 'vpc_authorize_id':
                return Mage::helper('payment')->__('Authorize ID');
            case 'vpc_order_info':
                return Mage::helper('payment')->__('Order Info');
            case 'vpc_receipt_no':
                return Mage::helper('payment')->__('Receipt No');
            case 'vpc_transaction_no':
                return Mage::helper('payment')->__('Transaction No');
            case 'vpc_merch_txn_ref':
                return Mage::helper('payment')->__('Merchant Transaction Reference');
            case 'vpc_batch_no':
                return Mage::helper('payment')->__('Batch No');
            case 'vpc_avs_result_code':
                return Mage::helper('payment')->__('AVS Result Code');
            case 'vpc_avs_response_code':
                return Mage::helper('payment')->__('AVS Response Code');
            case 'vpc_acq_avs_response_code':
                return Mage::helper('payment')->__('ACQ AVS Reponse Code');
            case 'vpc_risk_overall_result':
                return Mage::helper('payment')->__('Overall Result');
            case 'vpc_threedsenrolled':
                return Mage::helper('payment')->__('3D secure');
        }
        return '';
    }

    /**
     * Apply a filter upon value getting
     *
     * @param string $value
     * @param string $key
     * @return string
     */
    protected function _getValue($value, $key)
    {
        $label = '';
        switch ($key) {
            case 'vpc_avs_code':
                $label = $this->_getAvsLabel($value);
                break;
            case 'vpc_cvv2_match':
                $label = $this->_getCvv2Label($value);
                break;
            default:
                return $value;
        }
        return sprintf('#%s%s', $value, $value == $label ? '' : ': ' . $label);
    }

    /**
     * Attempt to convert AVS check result code into label
     *     
     * @param string $value
     * @return string
     */
    protected function _getAvsLabel($value)
    {
        switch ($value) {
            // Visa, MasterCard, Discover and American Express
            case 'A':
                return Mage::helper('payment')->__('Matched Address only (no ZIP)');
            case 'B': // international "A"
                return Mage::helper('payment')->__('Matched Address only (no ZIP). International');
            case 'N':
                return Mage::helper('payment')->__('No Details matched');
            case 'C': // international "N"
                return Mage::helper('payment')->__('No Details matched. International');
            case 'X':
                return Mage::helper('payment')->__('Exact Match. Address and nine-digit ZIP code');
            case 'D': // international "X"
                return Mage::helper('payment')->__('Exact Match. Address and Postal Code. International');
            case 'F': // UK-specific "X"
                return Mage::helper('payment')->__('Exact Match. Address and Postal Code. UK-specific');
            case 'E':
                return Mage::helper('payment')->__('N/A. Not allowed for MOTO (Internet/Phone) transactions');
            case 'G':
                return Mage::helper('payment')->__('N/A. Global Unavailable');
            case 'I':
                return Mage::helper('payment')->__('N/A. International Unavailable');
            case 'Z':
                return Mage::helper('payment')->__('Matched five-digit ZIP only (no Address)');
            case 'P': // international "Z"
                return Mage::helper('payment')->__('Matched Postal Code only (no Address)');
            case 'R':
                return Mage::helper('payment')->__('N/A. Retry');
            case 'S':
                return Mage::helper('payment')->__('N/A. Service not Supported');
            case 'U':
                return Mage::helper('payment')->__('N/A. Unavailable');
            case 'W':
                return Mage::helper('payment')->__('Matched whole nine-didgit ZIP (no Address)');
            case 'Y':
                return Mage::helper('payment')->__('Yes. Matched Address and five-didgit ZIP');
            // Maestro and Solo
            case '0':
                return Mage::helper('payment')->__('All the address information matched');
            case '1':
                return Mage::helper('payment')->__('None of the address information matched');
            case '2':
                return Mage::helper('payment')->__('Part of the address information matched');
            case '3':
                return Mage::helper('payment')->__('N/A. The merchant did not provide AVS information');
            case '4':
                return Mage::helper('payment')->__('N/A. Address not checked, or acquirer had no response. Service not available');
            default:
                return $value;
        }
    }

    /**
     * Attempt to convert CVV2 check result code into label
     *     
     * @param string $value
     * @return string
     */
    protected function _getCvv2Label($value)
    {
        switch ($value) {
            // Visa, MasterCard, Discover and American Express
            case 'M':
                return Mage::helper('payment')->__('Matched (CVV2CSC)');
            case 'N':
                return Mage::helper('payment')->__('No match');
            case 'P':
                return Mage::helper('payment')->__('N/A. Not processed');
            case 'S':
                return Mage::helper('payment')->__('N/A. Service not supported');
            case 'U':
                return Mage::helper('payment')->__('N/A. Service not available');
            case 'X':
                return Mage::helper('payment')->__('N/A. No response');
            // Maestro and Solo
            case '0':
                return Mage::helper('payment')->__('Matched (CVV2)');
            case '1':
                return Mage::helper('payment')->__('No match');
            case '2':
                return Mage::helper('payment')->__('N/A. The merchant has not implemented CVV2 code handling');
            case '3':
                return Mage::helper('payment')->__('N/A. Merchant has indicated that CVV2 is not present on card');
            case '4':
                return Mage::helper('payment')->__('N/A. Service not available');
            default:
                return $value;
        }
    }

    /**
     * Attempt to convert centinel VPAS result into label
     *
     * @param string $value
     * @return string
     */
    private function _getCentinelVpasLabel($value)
    {
        switch ($value) {
            case '2':
            case 'D':
                return Mage::helper('payment')->__('Authenticated, Good Result');
            case '1':
                return Mage::helper('payment')->__('Authenticated, Bad Result');
            case '3':
            case '6':
            case '8':
            case 'A':
            case 'C':
                return Mage::helper('payment')->__('Attempted Authentication, Good Result');
            case '4':
            case '7':
            case '9':
                return Mage::helper('payment')->__('Attempted Authentication, Bad Result');
            case '':
            case '0':
            case 'B':
                return Mage::helper('payment')->__('No Liability Shift');
            default:
                return $value;
        }
    }
}
