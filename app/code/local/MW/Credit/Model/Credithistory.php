<?php

class MW_Credit_Model_Credithistory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('credit/credithistory');
    }
    
	private function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
    
	/**
	 * 
	 * @param $data = array('type_transaction', 
	 * 						'transaction_detail', 
	 * 						'amount', 
	 * 						'beginning_transaction', 
	 * 						'end_transaction',
	 * 						'created_time')
	 * @return void
	 */
    public function saveTransactionHistory($data = array(), $customerId = null)
    {
    	/**
    	 * if exits $customerId, action is RECIVE from somebody(Friend or Admin), 
    	 * else you is current customer
    	 */
    	if($customerId != null){
    		$data['customer_id'] = $customerId;
    	}else{
    		$data['customer_id'] = $this->_getCustomer()->getId();
    	}
    	
    	if($data['type_transaction'] == MW_Credit_Model_TransactionType::USE_TO_CHECKOUT){
    		$data['status'] = MW_Credit_Model_OrderStatus::PENDING;
    	}else{
    		$data['status'] = MW_Credit_Model_OrderStatus::COMPLETE;
    	}
    	
    	$history = Mage::getModel('credit/credithistory');
    	$history->setData($data);
    	$history->save();
    }
	
	public function sendInvitation($current,$new,$send,$mail) 
	{
		
	// $templateId can use Id of transactional emails (found in "System->Trasactional Emails").
	$templateId = Mage::getStoreConfig('credit_options/mail_info/member_email_template');

	// $sender can set identity of diffrent Store emails
	// found in "System->Configuration->General->Store Email Addresses"
	$sender = Mage::getStoreConfig('credit_options/mail_info/sender_email_identity');
	
		try {
			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->setDesignConfig(array('area' => 'frontend',
												 'store' => Mage::app()->getStore()->getId()
												 ))
						->sendTransactional(
							$templateId,
							$sender,
							$mail,
                            null,	// name, not test
							array(
								'recipients'	=> '',
								'sender'	=>	'',
								'currenCredit'	=>	$current,
								'newCredit'	=>	$new,
								'sendCredit'	=>	$send
							)
						);
			if (!$mailTemplate->getSentSuccess()) {
				return false;
			} else {
				return true;
			}
		} catch (Exception $e) {
			//echo $e;
			return false;
		}
	}
}