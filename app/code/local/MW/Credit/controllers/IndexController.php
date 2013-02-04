<?php
class MW_Credit_IndexController extends Mage_Core_Controller_Front_Action
{
	// const setup in config etc, reference current id in config
	const EMAIL_TO_SENDER_TEMPLATE_XML_PATH 	= 'credit/mail_info/sender_template';
    const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH 	= 'credit/mail_info/recipient_template';
    const XML_PATH_EMAIL_IDENTITY				= 'credit/mail_info/email_sender';
	
    private function _getSession()
    {
        return Mage::getSingleton('core/session');
    }
    
    private function _goBack()
    {
    	return $this->_redirect('credit');
    }
    
	private function _getHelper()
    {
    	return Mage::helper('credit');
    }
	
	/**
	 * 
	 * @return Mage_Core_Model_Abstract
	 */
	private function _getCustomer() 
	{
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		return Mage::getSingleton('credit/creditcustomer')->load($customerId);	
	}
	
	private function _getFriendByEmail($email)
	{
		$friend = Mage::getModel('customer/customer')->getCollection()
				  		->addAttributeToFilter('email', $email)
						->addAttributeToSelect('*');
		foreach($friend as $_friend){
			 return Mage::getSingleton('credit/creditcustomer')->load($_friend->getId());	
		}
	}
	
	/**
	 * Process sent mail
	 * 
	 * @param string $emailto
	 * @param string $template
	 * @param array $data
	 * @return void
	 */
   	private function _sendEmailTransaction($emailto, $template, $data)
   	{
		$storeId = Mage::app()->getStore()->getId();  
   		$templateId = Mage::getStoreConfig($template,$storeId);  
		
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);
		  
		  try{
			  Mage::getModel('core/email_template')->sendTransactional(
			      $templateId, 
			      Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId), 
			      $emailto,
			      null,	// name sender, not describle
			      $data, 
			      $storeId);
			  $translate->setTranslateInline(true);
		  }catch(Exception $e){
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}
	
    public function preDispatch()
    {
        parent::preDispatch();
    	if (!$this->getRequest()->isDispatched()) {
            return;
        }
    	if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
    }
    
    public function indexAction()
    {	
    	if(!Mage::getStoreConfig('credit/config/enabled')){
			$this->norouteAction();
			return;
		}
		$this->loadLayout();  
		$this->_initLayoutMessages('customer/session');	//  load message session
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Credit'));		
		$this->renderLayout();
    }

	public function sendAction()
	{
		if ($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost(); //echo "<pre>";var_dump($post);die();
			
			if($this->_getHelper()->enabledCapcha()){
				$require = dirname(dirname(__FILE__))."/Helper/Capcha/Securimage.php";
				require($require);
				$img = new Securimage();
				$valid = $img->check($post['code']);
				if(!$valid) {
			      	$this->_getSession()->addError("Your Security code is incorrect. Please try again!");
			       	$this->_goBack();
			       	return;
			    }
			}
			
			
			// loop through each record
			foreach ($post['contacts'] as $value){
				$email = $value['email'];
				$amountCredit = (int) $value['credit'];	//send Credit
				$amountCredit = abs($amountCredit);	// validate credit is allow
						 
				$oldCredit = $this->_getCustomer()->getCredit();	// current credit customer
			    // if credit is gretter than credit customer
		        if ($amountCredit > $oldCredit) {
		      		$this->_getSession()->addError(
		            	$this->__('Credit "%s" is not enough.', Mage::helper('core')->htmlEscape($amountCredit))
		          	);
		          	$this->_goBack();
		          	return;
		        }
		        
		        // if not exist email
		       	if($this->_getFriendByEmail($email) == null) {
		       		$this->_getSession()->addError("Email: $email is not exits. Please try again!");
		       		$this->_goBack();
		          	return;
		       	}
		       	
		    	// max credit for each send
	 	        if(	(Mage::helper('credit')->getMaxCreditToSend() < $amountCredit) && 
        			(Mage::helper('credit')->getMaxCreditToSend() != 0) ){
        		
        			$this->_getSession()->addError(
        				$this->__('You only choose the credit less than or equal %s',Mage::helper('credit')->getMaxCreditToSend())
        			);	
        			$this->_goBack();
          			return;
        		}
		       	
		        
		
				// get friend by email
				$friendId = $this->_getFriendByEmail($email)->getId();
				$oldFriendCredit = $this->_getFriendByEmail($email)->getCredit();
		        
				if( $this->_getCustomer()->getId() != $friendId ) {
					$newCredit = $oldCredit - $amountCredit;
					$newFriendCredit = $oldFriendCredit + $amountCredit;

					// save credit for friend
					$this->_getFriendByEmail($email)->setCredit($newFriendCredit)->save();
					
					//save credit for send customer
					$this->_getCustomer()->setCredit($newCredit)->save();
									
					// Save history transaction for customer
	            	$historyData = array('type_transaction'=>MW_Credit_Model_TransactionType::SEND_TO_FRIEND, 
	            					     'transaction_detail'=>$friendId, 
	            						 'amount'=>-$amountCredit, 
	            						 'beginning_transaction'=>$oldCredit,
	            						 'end_transaction'=>$newCredit,
	            					     'created_time'=>now());
	            	Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData);					
		
					// Save history transaction for friend
	            	$historyData = array('type_transaction'=>MW_Credit_Model_TransactionType::RECIVE_FROM_FRIEND, 
            	     					 'transaction_detail'=>$this->_getCustomer()->getId(), 
            		 					 'amount'=>$amountCredit, 
            		 					 'beginning_transaction'=>$oldFriendCredit,
	            						 'end_transaction'=>$newFriendCredit,
            	     					 'created_time'=>now());
	            	Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData, $friendId);

					$this->_getSession()->addSuccess("You send $amountCredit Credit to friend $email");
					
					//Send mail to friend
					if(Mage::getStoreConfig('credit/mail_info/enable_send_email_to_recipient')){
						$arrData = array('senderName'=>Mage::getSingleton('customer/session')->getCustomer()->getFirstname(),
	                  					 'senderMail'=>Mage::getSingleton('customer/session')->getCustomer()->getEmail(),
										 'recipientAmountCredit'=>$amountCredit,
										 'recipientNewCredit'=>$newFriendCredit,
										 'recipientName'=>Mage::getSingleton('customer/customer')->load($friendId)->getFirstname());
						//echo "<pre>";var_dump($arrData);die();
						$mailto = $email;
						$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
						$postObject = new Varien_Object();
						$postObject->setData($arrData);
						$this->_sendEmailTransaction($mailto, $template, $postObject->getData());
					}
					
					//Send mail to me
					if(Mage::getStoreConfig('credit/mail_info/enable_send_email_to_sender')){
						$arrData = array('senderName'=>Mage::getSingleton('customer/session')->getCustomer()->getFirstname(),
	                  					 'senderMail'=>Mage::getSingleton('customer/session')->getCustomer()->getEmail(),
										 'senderAmountCredit'=>$amountCredit,
										 'senderNewCredit'=>$newCredit,
										 'recipientName'=>Mage::getSingleton('customer/customer')->load($friendId)->getFirstname());
						//echo "<pre>";var_dump($arrData);die();
						$mailto = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
						$template = self::EMAIL_TO_SENDER_TEMPLATE_XML_PATH;
						$postObject = new Varien_Object();
						$postObject->setData($arrData);
						$this->_sendEmailTransaction($mailto, $template, $postObject->getData());
					}
					
				}else{
					$this->_getSession()->addError("Email: $email is your email address. Please try again!");
				}
			}
		}
		$this->_goBack();
	}
	
	public function exchangeAction()
	{
		if(!Mage::getStoreConfig('credit/config/enabled')){
			$this->norouteAction();
			return;
		}
		if(!Mage::helper('credit')->allowExchangeCreditToPoint()){
			$this->norouteAction();
			return;
		}
		
		$rate = Mage::helper('credit')->getCreditPerPoint();
		$credit = $this->getRequest()->getPost('exchange_credit');
		$point = ($credit * $rate[1])/$rate[0];
		
		Mage::dispatchEvent('exchange_credit_to_rewardpoints', array(
			'customer'	=> $this->_getCustomer(),
            'credit'    => $point
        ));
	}
	
	public function imageAction()
	{
		$require = dirname(dirname(__FILE__))."/Helper/Capcha/Securimage.php";
		require($require);
		
		$hp = $this->_getHelper();
		$img = new Securimage();
		
		//Change some settings
		$img->image_width = $hp->getCapchaImageWidth();
		$img->image_height = $hp->getCapchaImageHeight();
		$img->perturbation =$hp->getCapchaPerturbation();
		$img->code_length = $hp->getCapchaCodeLength();
		$img->image_bg_color = new Securimage_Color("#ffffff");	// not set config
		$img->use_transparent_text = $hp->capchaUseTransparentText();
		$img->text_transparency_percentage = $hp->getCapchaTextTransparencyPercentage(); // 100 = completely transparent
		$img->num_lines = $hp->getCapchaNumberLine();
		$img->image_signature = '';
		$img->text_color = new Securimage_Color("#000000");
		$img->line_color = new Securimage_Color("#cccccc");
		$backgroundFile = $hp->getCapchaBackgroundImage();
		$img->show($backgroundFile);
				
	}

}