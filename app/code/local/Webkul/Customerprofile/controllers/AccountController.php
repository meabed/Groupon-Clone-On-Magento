<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Webkul_Customerprofile_AccountController extends Mage_Customer_AccountController
{

    public function preDispatch()
    {
        // a brute-force protection here would be nice

		if ( !strstr($this->getRequest()->getRequestUri(), 'customerprofile/account/view/nickname') ) {
			parent::preDispatch();
		}

    }

	public function viewAction() {

		if ( trim($this->getRequest()->getParam('nickname')) == ''  ) {
			$this->_redirect('home');

		} else {

			$nickname = $this->getRequest()->getParam('nickname');

			$this->loadLayout( array(
			                'default',
			                'customerprofile_account_view'
			            ));

			$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Profile'));
        	$this->renderLayout();
		}

	}
	/* make payment details */
	public function paymentAction()
	 {
		$wholedata=$this->getRequest()->getParams();
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
		$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
		$readresult=$write->query("select * from ".$mysqlprefix."customerpartner_entity_datapayment where userid = '".$wholedata['userid']."'");
		$row = $readresult->fetch();
		#print_r($row);
		#echo $row['userid'];
		if($row['userid']>0)
		{
			$result=$write->query("update  ".$mysqlprefix."customerpartner_entity_datapayment set paymentsource='".$wholedata['paymentsource']."' where userid='".$wholedata['userid']."'");
		}
		else
		{
			$result=$write->query("insert into ".$mysqlprefix."customerpartner_entity_datapayment values('','".$wholedata['userid']."','".$wholedata['paymentsource']."')");
		}
		
		$this->_redirect('customer/account/edit/');
		$this->_getSession()->addSuccess($this->__('Your Payment Information Is Sucessfully Saved.'));
			
	 }
	/**
     * Create customer account action
     */
    public function createPostAction()
    {	$session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }
			/* here i code for making db entry in my table when new user added */
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			$wholedata=$this->getRequest()->getParams();
			$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
			$write->query("insert into ".$mysqlprefix."customerpartner_entity_userdata values('','".$wholedata['firstname']."','".$wholedata['lastname']."','".$wholedata['wantpartner']."','".$wholedata['email']."','')");
			/* end here i code for making db entry in my table when new user added */
            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);

                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }
		$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
    
		
	}
	public function editProfileAction()
    {

        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/*/edit');
        }

        if ($this->getRequest()->isPost()) {
        	$customer = Mage::getModel('customer/customer')->load($this->_getSession()->getCustomerId());
            $customer->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId())
                ->setNickname($this->_getSession()->getCustomer()->getNickname());

            $errors = array();
            $fields = $this->getRequest()->getParams();
            foreach ($fields as $code=>$value) {
            	if ( $code != 'form_key' ) {
                	if ( ($error = $this->validateProfileFields($code, $value, $customer->getId())) !== true ) {
						$errors[] = $error;
                	} else {
						if ( $code == 'nickname' ) {

							#remove the old urlrewrite
							$uldURLCollection = Mage::getModel('core/url_rewrite')->getResourceCollection();
							$uldURLCollection->getSelect()
								->where('id_path=?', 'customerprofile/'.strtolower($customer->getNickname()));

							$uldURLCollection->setPageSize(1)->load();

							if ( $uldURLCollection->count() > 0 ) {
								$uldURLCollection->getFirstItem()->delete();
							}

							#add url rewrite
			                $modelURLRewrite = Mage::getModel('core/url_rewrite');

			                $modelURLRewrite->setIdPath('customerprofile/'.strtolower($value))
			                    ->setTargetPath('customerprofile/account/view/nickname/'.$value)
			                    ->setOptions('')
			                    ->setDescription(null)
			                    ->setRequestPath($value);

			                $modelURLRewrite->save();

						}
						#save the new value
						$customer->setData($code, $value);
                	}
                }
            }

            if ( isset($_FILES['avatar']) && $_FILES['avatar']['name'] != '' ) {
            	if ( ( $error = $this->uploadAvatar($customer->getId()) ) !== true ) {
            		$errors[] = $error;
            	}
            }

            if ($this->_getSession()->getCustomerGroupId()) {
                $customer->setGroupId($this->_getSession()->getCustomerGroupId());
            }

            try {

                $customer->save();

                $this->_getSession()->setCustomer($customer);

				if (!empty($errors)) {
	                foreach ($errors as $message) {
	                    $this->_getSession()->addError($message);
	                }
	            } else {
	            	$this->_getSession()->addSuccess($this->__('Profile information was successfully saved'));
	            }

                $this->_redirect('customer/account/edit');
                return;
            } catch (Mage_Core_Exception $e) {
            	#->setCustomerFormData($this->getRequest()->getPost())
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
            	#->setCustomerFormData($this->getRequest()->getPost())
                $this->_getSession()->addException($e, $this->__('Can\'t save customer'));
            }
        }

        $this->_redirect('customer/*/*');
    }

	private function uploadAvatar($avatar_name) {
		$max_size = 3670016; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadimage');
		$my_upload->upload_dir = Mage::getBaseDir().'/media/avatar/'; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".gif", ".jpg",".jpeg",".png"); // specify the allowed extensions here
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->rename_file = true;
		$my_upload->filename =	$avatar_name;

		$my_upload->the_temp_file = $_FILES['avatar']['tmp_name'];
		$my_upload->the_file = $_FILES['avatar']['name'];
		$my_upload->http_error = $_FILES['avatar']['error'];
		$my_upload->replace = true; #false; #(isset($_POST['replace'])) ? $_POST['replace'] : "n"; // because only a checked checkboxes is true
		$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "y"; // use this boolean to check for a valid filename
		$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";

		if ($my_upload->upload($new_name)) { // new name is an additional filename information, use this to rename the uploaded file
			return true;
			#$full_path = $my_upload->upload_dir.$my_upload->file_copy;
			#$info = $my_upload->get_uploaded_file_info($full_path);
			// ... or do something like insert the filename to the database
		} else {
			return $my_upload->show_error_string();
			#$this->_getSession()->addError($my_upload->show_error_string());
			#Mage::getSingleton('core/session')->addError($my_upload->show_error_string());
			#return false;
		}



	}

	private function validateProfileFields($code, $value, $customer_id) {
		switch ($code) {
			case 'nickname':
				#check if this nikname is a-z09_
				#if ( !preg_match('/^([a-Z0-9_]+)$/', $value) ) {
				if ( !preg_match('/^[a-z0-9]+$/i', $value) ) {
					return 'Your nickname should contain only letters, numbers and underscore';
				} elseif ( strlen($value) < 4 ) {
					return 'Your nickname should be at least 4 characters long';
				} else {
					#check if another customer has it
					$collection = Mage::getResourceModel('customer/customer_collection')
									->addAttributeToFilter('nickname', $value)
									->addAttributeToFilter('entity_id', array('nin' => $customer_id) );
					if ( $collection->count() > 0 ) {
						return 'Your nickname should be unique';
					}
				}
			break;
		}
		return true;
	}

}
?>
