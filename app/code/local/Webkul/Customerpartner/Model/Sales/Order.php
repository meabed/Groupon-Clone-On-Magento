<?php

class Webkul_Customerpartner_Model_Sales_Order extends Mage_Sales_Model_Order
{


	protected function _afterSave()
	{
		parent::_afterSave();

		//Customerpartner Logic
		$customerPartner = Mage::getModel('customerpartner/customerpartner');
		//Percent set in admin that an customer earns with the sale of a product
		$percent = Mage::getStoreConfig('partners/partners_options/percent');
		//Order ID
		$orderId = $this->getId();
		//Real Order ID, like 1000000001
		$realOrderId = $this->getRealOrderId();
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
		foreach($this->getAllItems() as $item){

			try{

			//Get product data
			$productData = $item->getData();

			//Get product ID
			$magentoProductId = $productData['product_id'];

			//Check if this is a Customerpartner product
			$isCustomerProduct = $customerPartner->isCustomerProduct($magentoProductId);
			if($isCustomerProduct){

				$qtyOrdered = $productData['qty_ordered'];
				$priceOrdered = $productData['price'] * $qtyOrdered;
				$amountEarned =  ($percent * $priceOrdered) / 100;
				$customerProductId = $isCustomerProduct;

				//Save record in our internal db table
				/****** here i code for mysql prefix checking and execute code*****/
				$query = sprintf('INSERT INTO '.$mysqlprefix.'customerpartner_aux (product_id, order_id, quantity, percent, amount_earned, real_order_id, amount_owed) VALUES (%d, %d, %d, %f, %f, %d, %f)',
								$customerProductId,
								$orderId,
								$qtyOrdered,
								$percent,
								$amountEarned,
								$realOrderId,
								$amountEarned
								);
				/** end **/
				/*$query = sprintf('INSERT INTO customerpartner_aux (product_id, order_id, quantity, percent, amount_earned, real_order_id, amount_owed) VALUES (%d, %d, %d, %f, %f, %d, %f)',
								$customerProductId,
								$orderId,
								$qtyOrdered,
								$percent,
								$amountEarned,
								$realOrderId,
								$amountEarned
								);*/
				$customerPartner->customQuery($query, 'write');
				

				$isCPProduct = Mage::getModel('customerpartner/customerpartner')->isCustomerProduct($magentoProductId);
				$cproduct = Mage::getModel('customerpartner/customerpartner_product')->load($isCPProduct);
				$customer = Mage::getModel('customer/customer')->load($cproduct->getCustomerId());
					/* chnage by me to get customer name*/
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					/* end chnage by me to get customer name*/
					mail($customer["email"],"PARTNER's STORE","YOUR PRODUCT HAS BEEN SOLD");
					$this->mymail();
					$ee = Mage::getModel('core/email_template')->loadDefault('customerpartner_email');
					$aa = array();
					$aa['myvar1'] =$realOrderId;
					$aa['myvar2'] = $cfname;
					$aa['myvar3'] = $qtyOrdered;
					$aa['myvar4'] = $priceOrdered;
					$processedTemplate = $ee->getProcessedTemplate($aa);
					$ee->setSenderName('Sales Department');
					$ee->setSenderEmail('sales@store.com');
					$ee->send($customer["email"],$cfname, $aa);

			}

			}catch(Exception $e){

				Mage::logException($e);

			}

		}

		return $this;
	}
	public function mymail(){ 
	
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			$emailTemplate = Mage::getModel('core/email_template')->loadDefault('customerpartner_email_template');
			$emailTemplateVariables = array();
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			/****** here i code for mysql prefix checking and execute code*****/
			$readresult=$write->query("select role_id from ".$mysqlprefix."admin_role  where role_name = 'Administrators'");
			/** end **/
			#$readresult=$write->query("select role_id from admin_role  where role_name = 'Administrators'");
			$row = $readresult->fetch();
			/****** here i code for mysql prefix checking and execute code*****/
			$sql= 'select user_id from '.$mysqlprefix.'admin_role where parent_id  = "'.$row['role_id'].'" ';
			/** end **/
			#$sql= 'select user_id from admin_role where parent_id  = "'.$row['role_id'].'" ';
			foreach($write->fetchAll($sql) as $id)
				{
					/****** here i code for mysql prefix checking and execute code*****/
					$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$id['user_id']."'");
					/** end **/
					#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$id['user_id']."'");
					$rowadmin = $readresultadmin->fetch();
					$emailTemplateVariables['myvar1'] = $rowadmin['firstname']." ".$rowadmin['lastname'];
					$myname = $rowadmin['firstname']." ".$rowadmin['lastname'];
					$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
					$emailTemplate->setSenderName('Sales Department');
					$emailTemplate->setSenderEmail($rowadmin['email']);
					$emailTemplate->send($rowadmin['email'],$myname,$emailTemplateVariables);
				}
		
		
	}

}
