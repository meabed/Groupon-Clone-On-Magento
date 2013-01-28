<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Webkul_Customerpartner_PartnerproductsController extends Mage_Customer_AccountController
{

	public function indexAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_products'
		            ));

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

		$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Products'));
    	$this->renderLayout();

	}
	
	public function myproductslistAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_productlist'
		            ));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Product List'));
    	$this->renderLayout();
	}
	public function mydashboardAction() {
		$this->loadLayout( array(
		                'default',
		                'customer_account_dashboard' 
		            ));
		
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Dashboard'));
    	$this->renderLayout();
	}
	public function simpleproductAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_simpleproduct'
		            ));
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Product Type: Simple Product'));
    	$this->renderLayout();

	}
	public function virtualproductAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_virtualproduct'
		            ));
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Product Type: Virtual Product'));
    	$this->renderLayout();

	}
	public function downloadproductAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_downloadableproduct'
		            ));
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Product Type: Downloadable Product'));
    	$this->renderLayout();

	}
	public function editsimpleAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_simpleproductedit'
		            ));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Edit Simple Product'));
    	$this->renderLayout();

	}
	public function editvirtualAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_virtualproductedit'
		            ));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Edit Virtual Product'));
    	$this->renderLayout();

	}
	public function editdownloadAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_downloadableproductedit'
		            ));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Edit Downloadable Product'));
    	$this->renderLayout();

	}
	public function editAction() {

		$this->loadLayout( array(
		                'default',
		                'customer_account_productsedit'
		            ));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Products'));
    	$this->renderLayout();

	}
	public function editdownloadablePostAction() {
			$id = $this->getRequest()->getParam('productid');
			if ( $id !== false ) {
				list($data, $errors) = $this->validatePost();
				if ( !empty($errors) ) {
		        	foreach ($errors as $message) {
		                $this->_getSession()->addError($message);
		            }
					$this->_redirect('customer/products/edit/', array(
			                'id'    => $id
			            ));
				} else {

					$customerId = $this->_getSession()->getCustomer()->getid();
					$product = Mage::getResourceModel('customerpartner/customerpartner_product_collection')
								->addAttributeToSelect('*')
								->addAttributeToFilter('customer_id', $customerId)
								->addAttributeToFilter('entity_id', $id)
								->load()
								->getFirstItem();
					$product->setName($this->getRequest()->getParam('name'));
					$product->setDescription($this->getRequest()->getParam('description'));
					$product->setShortDescription($this->getRequest()->getParam('short_description'));
					$product->setPrice($this->getRequest()->getParam('price'));
					#$product->setWeight($this->getRequest()->getParam('weight'));
					$product->setStock($this->getRequest()->getParam('stock'));
					#$product->setStock($this->getRequest()->getParam('sku')); echo $this->getRequest()->getParam('');
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sku=$this->getRequest()->getParam('sku');
					$category=$this->getRequest()->getParam('category');
					$querydata=$write->query("update customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
					$querydata=$write->query("update customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					$product->save();
					$iname=array();
					$iext=array();
					$gnt=0;
					$type='';
			        if ( isset($_FILES) && count($_FILES) > 0 ) {
						{	
							$cnt=0;
							foreach($_FILES as $image ) {
								/*#print_r($image);
								#echo substr($image['name'],-3);
								if((substr($image['name'],-3)=='zip')&&($cnt==0)&&($gnt==2)){$chk=1;$nent=1; #echo 'mzip';
								}
								if((substr($image['name'],-3)!=='zip')&&($cnt==0)){$chk=2; #echo 'mjpg';
								}
								#if((substr($image['name'],-3)!=='zip')(substr($image['name'],-3)=='zip')&&($cnt==0)){$chk=2;}
								else {$chk=3; #echo 'third';
								}
								
								#echo 'cnt---'.$cnt;
								echo $image['name'];
								echo '<br>gnt---'.$gnt;*/
								
								$iname[0][$gnt]=$image['name'];
								$iext[1][$gnt]=strlen(substr($image['name'],-3));
								$gnt++;
								#echo '<br>';
							}
							#print_r($iname);
							#print_r($iext);
							#echo $iname[0][0];
							#echo $iname[0][$gnt-1];
							#echo $iext[1][$gnt-1];
							#echo $gnt;
							if(($iname[0][0]=='')&&($iext[1][0]<=0)&&($iname[0][$gnt-1]!='')&&($iext[1][$gnt-1]>0))
							#if(($iname[0][0]=='')&&($iext[1][0]=='')&&($iname[0][$gnt-1]!='')&&($iext[1][$gnt-1]=='zip'))
							{  $type='zip';}
							if(($iname[0][0]!='')&&($iext[1][0]>0)&&($iname[0][$gnt-1]=='')&&($iext[1][$gnt-1]<=0))
							#if(($iname[0][0]!='')&&($iext[1][0]!='')&&($iname[0][$gnt-1]=='')&&($iext[1][$gnt-1]==''))
							{ $type='img';}
							if(($iname[0][0]!='')&&($iext[1][0]>0)&&($iname[0][$gnt-1]!='')&&($iext[1][$gnt-1]>0))
							#if(($iname[0][0]!='')&&($iext[1][0]!='')&&($iname[0][$gnt-1]!='')&&($iext[1][$gnt-1]=='zip'))
							{ $type='both';}
							if($type=='zip')
							{	echo 'type is---'.$type;
								/* here is made chnage for new file */
									if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$id)) {
										
										foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$id) as $fileInfo) {
										if($fileInfo->isFile()){			
											#unlink($fileInfo->getPathname());
											#echo $fileInfo->isFile();
											#echo $fileInfo->getFilename();
											if((substr($fileInfo->getFilename(),-3)=='zip')||(substr($fileInfo->getFilename(),-3)=='rar')||(substr($fileInfo->getFilename(),-3)=='psd')||(substr($fileInfo->getFilename(),-3)=='pdf'))
											{unlink($fileInfo->getPathname());
											}
										}
										}
									}
								/* end here is made chnage for new file */
								/*foreach($_FILES as $image ) {	
										#print_r($image);
										#echo "image name-- ".$image['name']."--";
										if ( $image['tmp_name'] != '' )
											{
												if ( ( $error = $this->uploadZipSample($image, $id) ) !== true )
													$errors[] = $error;
											}	
								}	*/
								if ( $_FILES['uploadpack']['tmp_name'] != '' )
									{
										if (($error = $this->uploadZip($_FILES['uploadpack'],$id))!==true)
										$errors[] = $error;
									}
							}
							if($type=='img')
							{	echo 'type is---'.$type;
								/* here is made chnage for new file */
								/*
									if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$id)) {
										
										foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$id) as $fileInfo) {
										if($fileInfo->isFile()){			
											echo "<hr>unlik 1  -";
											unlink($fileInfo->getPathname());echo "file info is file - <hr>";
											echo $fileInfo->isFile();echo "file name -- <hr>";
											echo $fileInfo->getFilename();echo " subste is --  <hr>";
											echo substr($fileInfo->getFilename(),-3);echo "<hr>";
											if((substr($fileInfo->getFilename(),-3)=='zip')||(substr($fileInfo->getFilename(),-3)!='rar')||(substr($fileInfo->getFilename(),-3)!='psd')||(substr($fileInfo->getFilename(),-3)!='pdf')||(substr($fileInfo->getFilename(),-3)!='ZIP')||(substr($fileInfo->getFilename(),-3)!='RAR')||(substr($fileInfo->getFilename(),-3)!='PSD')||(substr($fileInfo->getFilename(),-3)!='PDF'))
											{
												echo 'unlink file is '.$fileInfo->getPathname();
												#unlink($fileInfo->getPathname());
												
											}
											
											
										}
										}
									}*/
								/* end here is made chnage for new file */
								foreach($_FILES as $image ) {	
										#print_r($image);
										#echo "image name-- ".$image['name']."--";
										if ( $image['tmp_name'] != '' )
											{
												if ( ( $error = $this->uploadZipSample($image, $id) ) !== true )
													$errors[] = $error;
											}	
								}	
								/*if ( $_FILES['uploadpack']['tmp_name'] != '' )
									{
										if (($error = $this->uploadZip($_FILES['uploadpack'],$id))!==true)
										$errors[] = $error;
									}*/
							}
							if($type=='both')
							{	echo 'type is---'.$type;
								/* here is made chnage for new file */
									if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$id)) {
										
										foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$id) as $fileInfo) {
										if($fileInfo->isFile()){			
											unlink($fileInfo->getPathname());
											#echo $fileInfo->isFile();
											#echo $fileInfo->getFilename();
											
										}
										}
									}
								/* end here is made chnage for new file */
								foreach($_FILES as $image ) {	
										#print_r($image);
										#echo "image name-- ".$image['name']."--";
										if ( $image['tmp_name'] != '' )
											{
												if ( ( $error = $this->uploadZipSample($image, $id) ) !== true )
													$errors[] = $error;
											}	
								}	
								if ( $_FILES['uploadpack']['tmp_name'] != '' )
									{
										if (($error = $this->uploadZip($_FILES['uploadpack'],$id))!==true)
										$errors[] = $error;
									}
							}
							/*else
							{
								foreach($_FILES as $image ) {	
										#print_r($image);
										#echo "image name-- ".$image['name']."--";
										if ( $image['tmp_name'] != '' )
											{
												if ( ( $error = $this->uploadZipSample($image, $id) ) !== true )
													$errors[] = $error;
											}	
								}	*/
								/*if ( $_FILES['uploadpack']['tmp_name'] != '' )
									{
										if (($error = $this->uploadZip($_FILES['uploadpack'],$id))!==true)
										$errors[] = $error;
									}*/
							#}
							/*foreach($_FILES as $image ) {	
									if ( $image['tmp_name'] != '' )
										{
											if ( ( $error = $this->uploadZipSample($image, $customer_partner_product_id) ) !== true )
												$errors[] = $error;
										}	
							}*/
							/*
							if ( $_FILES['dumimg']['tmp_name'] != '' )
								{
									if (($error = $this->uploadZipSample($_FILES['dumimg'],$id))!==true)
										$errors[] = $error;
								}*/
								
								/*foreach($_FILES as $image ) {
								if ( $image['tmp_name'] != '' ) {
									if ( ( $error = $this->uploadImage($image, $customer_partner_product_id) ) !== true ) {
										$errors[] = $error;
									}
								}

							}	*/
						}
						/*foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
					        	if ( ( $error = $this->uploadImage($image, $id) ) !== true ) {
					        		$errors[] = $error;
					        	}
							}

						}*/
			        }
					

			        if ( empty($errors) ) {
			        	$this->_getSession()->addSuccess($this->__('Your product was successfully updated'));
			        } else {
			        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
			        	foreach ($errors as $message) {
			                $this->_getSession()->addError($message);
			            }
			        }

					#$this->_redirect('customer/products/myproductslist/');

				}
			}
	}

	public function newAction()
	{
		unset($_SESSION['new_products_errors']);
		list($data, $errors) = $this->validatePost();
		$wholedata=$this->getRequest()->getParams();
		if ( empty($errors) ) {
			$data['customer_id'] = $this->_getSession()->getCustomer()->getid();
			$customer_partner_product_id = Mage::getModel('customerpartner/customerpartner')->createPartnerProduct($data);
			$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
			#$write->query("insert into customerpartner_entity_data values('','','".$wholedata['userid']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			/****** here i code for mysql prefix checking and execute code*****/
			$write->query("insert into ".$mysqlprefix."customerpartner_entity_data values('','".$wholedata['producttype']."','".$wholedata['producttypecustom']."','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/** end **/
			#$write->query("insert into customerpartner_entity_data values('','simple','none','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			if ( isset($_FILES) && count($_FILES) > 0 ) {
				if(($wholedata['producttype']=='simple' )||($wholedata['producttype']=='virtual' ) )
					{
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
								if ( ( $error = $this->uploadImage1($image, $customer_partner_product_id) ) !== true ) {
									$errors[] = $error;
								}
							}

						}
					}				
	        }
			if($wholedata['producttype']=='downloadable')	
				{
					if ( isset($_FILES) && count($_FILES) > 0 ) {
								
							foreach($_FILES as $image ) {	
									if ( $image['tmp_name'] != '' )
										{
											if ( ( $error = $this->uploadZipSample1($image, $customer_partner_product_id) ) !== true )
												$errors[] = $error;
										}	
							}			
							if ( $_FILES['uploadpack']['tmp_name'] != '' )
								{
									if ( ( $error = $this->uploadZip1($_FILES['uploadpack'], $customer_partner_product_id) ) !== true )
									$errors[] = $error;
								}
					}
			}
			if ( empty($errors) ) {
	        	$this->_getSession()->addSuccess($this->__('Your new product was successfully saved'));
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
	        } else {
	        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
	        	foreach ($errors as $message) {
	                $this->_getSession()->addError($message);
	            }
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
				/*$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$readresult=$write->query("select role_id from admin_role  where role_name = 'Administrators'");
					$row = $readresult->fetch();
					$sql= 'select user_id from admin_role where parent_id  = "'.$row['role_id'].'" ';
					foreach($write->fetchAll($sql) as $adm)
						{
							$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
							$rowadmin = $readresultadmin->fetch();
							$emailTempVariables['myvar1'] = $wholedata['name'];
							$emailTempVariables['myvar2'] =$categoryname;
							$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
							$emailTemp->setSenderName($cfname);
							$emailTemp->setSenderEmail($cmail);
							$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
						}*/
	        }
		} else {
        	foreach ($errors as $message) {
                $this->_getSession()->addError($message);
            }
            $_SESSION['new_products_errors'] = $data;
		}
		$this->_redirect('customer/products/');
	}
	public function virtualnewAction()
	{
		unset($_SESSION['new_products_errors']);
		list($data, $errors) = $this->validatePost();
		$wholedata=$this->getRequest()->getParams();
		if ( empty($errors) ) {
			$data['customer_id'] = $this->_getSession()->getCustomer()->getid();
			$customer_partner_product_id = Mage::getModel('customerpartner/customerpartner')->createPartnerProduct($data);
			$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
			#$write->query("insert into customerpartner_entity_data values('','','".$wholedata['userid']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			/****** here i code for mysql prefix checking and execute code*****/
			$write->query("insert into ".$mysqlprefix."customerpartner_entity_data values('','".$wholedata['producttype']."','".$wholedata['producttypecustom']."','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/** end **/
			#$write->query("insert into customerpartner_entity_data values('','simple','none','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			if ( isset($_FILES) && count($_FILES) > 0 ) {
				if(($wholedata['producttype']=='simple' )||($wholedata['producttype']=='virtual' ) )
					{
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
								if ( ( $error = $this->uploadImage1($image, $customer_partner_product_id) ) !== true ) {
									$errors[] = $error;
								}
							}

						}
					}				
	        }
			if($wholedata['producttype']=='downloadable')	
				{
					if ( isset($_FILES) && count($_FILES) > 0 ) {
								
							foreach($_FILES as $image ) {	
									if ( $image['tmp_name'] != '' )
										{
											if ( ( $error = $this->uploadZipSample1($image, $customer_partner_product_id) ) !== true )
												$errors[] = $error;
										}	
							}			
							if ( $_FILES['uploadpack']['tmp_name'] != '' )
								{
									if ( ( $error = $this->uploadZip1($_FILES['uploadpack'], $customer_partner_product_id) ) !== true )
									$errors[] = $error;
								}
					}
			}
			if ( empty($errors) ) {
	        	$this->_getSession()->addSuccess($this->__('Your new product was successfully saved'));
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
	        } else {
	        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
	        	foreach ($errors as $message) {
	                $this->_getSession()->addError($message);
	            }
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
				/*$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$readresult=$write->query("select role_id from admin_role  where role_name = 'Administrators'");
					$row = $readresult->fetch();
					$sql= 'select user_id from admin_role where parent_id  = "'.$row['role_id'].'" ';
					foreach($write->fetchAll($sql) as $adm)
						{
							$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
							$rowadmin = $readresultadmin->fetch();
							$emailTempVariables['myvar1'] = $wholedata['name'];
							$emailTempVariables['myvar2'] =$categoryname;
							$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
							$emailTemp->setSenderName($cfname);
							$emailTemp->setSenderEmail($cmail);
							$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
						}*/
	        }
		} else {
        	foreach ($errors as $message) {
                $this->_getSession()->addError($message);
            }
            $_SESSION['new_products_errors'] = $data;
		}
		$this->_redirect('customerpartner/partnerproducts/virtualproduct/');
	}
	public function simplenewAction()
	{
		unset($_SESSION['new_products_errors']);
		list($data, $errors) = $this->validatePost();
		$wholedata=$this->getRequest()->getParams();
		if ( empty($errors) ) {
			$data['customer_id'] = $this->_getSession()->getCustomer()->getid();
			$customer_partner_product_id = Mage::getModel('customerpartner/customerpartner')->createPartnerProduct($data);
			$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
			#$write->query("insert into customerpartner_entity_data values('','','".$wholedata['userid']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			/****** here i code for mysql prefix checking and execute code*****/
			$write->query("insert into ".$mysqlprefix."customerpartner_entity_data values('','".$wholedata['producttype']."','".$wholedata['producttypecustom']."','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/** end **/
			#$write->query("insert into customerpartner_entity_data values('','simple','none','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			if ( isset($_FILES) && count($_FILES) > 0 ) {
				if(($wholedata['producttype']=='simple' )||($wholedata['producttype']=='virtual' ) )
					{
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
								if ( ( $error = $this->uploadImage1($image, $customer_partner_product_id) ) !== true ) {
									$errors[] = $error;
								}
							}

						}
					}				
	        }
			if($wholedata['producttype']=='downloadable')	
				{
					if ( isset($_FILES) && count($_FILES) > 0 ) {
								
							foreach($_FILES as $image ) {	
									if ( $image['tmp_name'] != '' )
										{
											if ( ( $error = $this->uploadZipSample1($image, $customer_partner_product_id) ) !== true )
												$errors[] = $error;
										}	
							}			
							if ( $_FILES['uploadpack']['tmp_name'] != '' )
								{
									if ( ( $error = $this->uploadZip1($_FILES['uploadpack'], $customer_partner_product_id) ) !== true )
									$errors[] = $error;
								}
					}
			}
			if ( empty($errors) ) {
	        	$this->_getSession()->addSuccess($this->__('Your new product was successfully saved'));
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
	        } else {
	        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
	        	foreach ($errors as $message) {
	                $this->_getSession()->addError($message);
	            }
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
				/*$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$readresult=$write->query("select role_id from admin_role  where role_name = 'Administrators'");
					$row = $readresult->fetch();
					$sql= 'select user_id from admin_role where parent_id  = "'.$row['role_id'].'" ';
					foreach($write->fetchAll($sql) as $adm)
						{
							$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
							$rowadmin = $readresultadmin->fetch();
							$emailTempVariables['myvar1'] = $wholedata['name'];
							$emailTempVariables['myvar2'] =$categoryname;
							$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
							$emailTemp->setSenderName($cfname);
							$emailTemp->setSenderEmail($cmail);
							$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
						}*/
	        }
		} else {
        	foreach ($errors as $message) {
                $this->_getSession()->addError($message);
            }
            $_SESSION['new_products_errors'] = $data;
		}
		$this->_redirect('customerpartner/partnerproducts/simpleproduct/');
	}
	public function downloadablenewAction()
	{
		unset($_SESSION['new_products_errors']);
		list($data, $errors) = $this->validatePost();
		$wholedata=$this->getRequest()->getParams();
		if ( empty($errors) ) {
			$data['customer_id'] = $this->_getSession()->getCustomer()->getid();
			$customer_partner_product_id = Mage::getModel('customerpartner/customerpartner')->createPartnerProduct($data);
			$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
			#$write->query("insert into customerpartner_entity_data values('','','".$wholedata['userid']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			/****** here i code for mysql prefix checking and execute code*****/
			$write->query("insert into ".$mysqlprefix."customerpartner_entity_data values('','".$wholedata['producttype']."','".$wholedata['producttypecustom']."','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			/** end **/
			#$write->query("insert into customerpartner_entity_data values('','simple','none','','".$wholedata['userid']."','".$wholedata['wstoreids']."','".$wholedata['sku']."','$customer_partner_product_id','".$wholedata['category']."')");
			if ( isset($_FILES) && count($_FILES) > 0 ) {
				if(($wholedata['producttype']=='simple' )||($wholedata['producttype']=='virtual' ) )
					{
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
								if ( ( $error = $this->uploadImage1($image, $customer_partner_product_id) ) !== true ) {
									$errors[] = $error;
								}
							}

						}
					}				
	        }
			if($wholedata['producttype']=='downloadable')	
				{
					if ( isset($_FILES) && count($_FILES) > 0 ) {
								
							foreach($_FILES as $image ) {	
									if ( $image['tmp_name'] != '' )
										{
											if ( ( $error = $this->uploadZipSample1($image, $customer_partner_product_id) ) !== true )
												$errors[] = $error;
										}	
							}			
							if ( $_FILES['uploadpack']['tmp_name'] != '' )
								{
									if ( ( $error = $this->uploadZip1($_FILES['uploadpack'], $customer_partner_product_id) ) !== true )
									$errors[] = $error;
								}
					}
			}
			if ( empty($errors) ) {
	        	$this->_getSession()->addSuccess($this->__('Your new product was successfully saved'));
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
	        } else {
	        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
	        	foreach ($errors as $message) {
	                $this->_getSession()->addError($message);
	            }
				$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
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
					foreach($write->fetchAll($sql) as $adm)
					{
						/****** here i code for mysql prefix checking and execute code*****/
						$readresultadmin=$write->query("select * from  ".$mysqlprefix."admin_user  where user_id = '".$adm['user_id']."'");
						/** end **/
						#$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
						$rowadmin = $readresultadmin->fetch();
						$emailTempVariables['myvar1'] = $wholedata['name'];
						$emailTempVariables['myvar2'] =$categoryname;
						$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
						$emailTemp->setSenderName($cfname);
						$emailTemp->setSenderEmail($cmail);
						$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
					}
				/*$customer = Mage::getModel('customer/customer')->load($wholedata['userid']);
					$cfname=$customer->getFirstname()." ".$customer->getLastname();
					$cmail=$customer->getEmail();
					$catagory_model = Mage::getModel('catalog/category');
					$categoriesy = $catagory_model->load($wholedata['category']);
					$categoryname=$categoriesy->getName();
					$emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
					$emailTempVariables = array();
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$readresult=$write->query("select role_id from admin_role  where role_name = 'Administrators'");
					$row = $readresult->fetch();
					$sql= 'select user_id from admin_role where parent_id  = "'.$row['role_id'].'" ';
					foreach($write->fetchAll($sql) as $adm)
						{
							$readresultadmin=$write->query("select * from  admin_user  where user_id = '".$adm['user_id']."'");
							$rowadmin = $readresultadmin->fetch();
							$emailTempVariables['myvar1'] = $wholedata['name'];
							$emailTempVariables['myvar2'] =$categoryname;
							$emailTempVariables['myvar3'] =$rowadmin['firstname']." ".$rowadmin['lastname'];$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
							$emailTemp->setSenderName($cfname);
							$emailTemp->setSenderEmail($cmail);
							$emailTemp->send($rowadmin['email'],$rowadmin['firstname']." ".$rowadmin['lastname'],$emailTempVariables);
						}*/
	        }
		} else {
        	foreach ($errors as $message) {
                $this->_getSession()->addError($message);
            }
            $_SESSION['new_products_errors'] = $data;
		}
		$this->_redirect('customerpartner/partnerproducts/downloadproduct/');
	}
	public function editsimplePostAction() {
			$id = $this->getRequest()->getParam('productid');
			if ( $id !== false ) {
				list($data, $errors) = $this->validatePost();
				if ( !empty($errors) ) {
		        	foreach ($errors as $message) {
		                $this->_getSession()->addError($message);
		            }
					$this->_redirect('customer/products/edit/', array(
			                'id'    => $id
			            ));
				} else {
					$customerId = $this->_getSession()->getCustomer()->getid();
					/****** here i code for mysql prefix *****/
					$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
					$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
					/****** end here i code for mysql prefix *****/
					$product = Mage::getResourceModel('customerpartner/customerpartner_product_collection')
								->addAttributeToSelect('*')
								->addAttributeToFilter('customer_id', $customerId)
								->addAttributeToFilter('entity_id', $id)
								->load()
								->getFirstItem();
					$product->setName($this->getRequest()->getParam('name'));
					$product->setDescription($this->getRequest()->getParam('description'));
					$product->setHighlights($this->getRequest()->getParam('highlight'));
					$product->setFinePrint($this->getRequest()->getParam('fineprint'));
					$product->setShortDescription($this->getRequest()->getParam('short_description'));
					$product->setPrice($this->getRequest()->getParam('price'));
					$product->setWeight($this->getRequest()->getParam('weight'));
					$product->setStock($this->getRequest()->getParam('stock'));
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sku=$this->getRequest()->getParam('sku');
					$category=$this->getRequest()->getParam('category');
					/****** here i code for mysql prefix checking and execute code*****/
					$querydata=$write->query("update ".$mysqlprefix."customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
						$querydata=$write->query("update ".$mysqlprefix."customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					/** end **/
					#$querydata=$write->query("update customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
					#$querydata=$write->query("update customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					$product->save();
					if ( $_FILES[images][name]!='' && count($_FILES) > 0 ) {
						if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$id)) {			
							foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$id) as $fileInfo) {
								if($fileInfo->isFile()){			
									unlink($fileInfo->getPathname());
								}
							}
						}
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
					        	if ( ( $error = $this->uploadImage($image, $id) ) !== true ) {
					        		$errors[] = $error;
					        	}
							}
						}
			        }
			        if ( empty($errors) ) {
			        	$this->_getSession()->addSuccess($this->__('Your product was successfully updated'));
			        } else {
			        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
			        	foreach ($errors as $message) {
			                $this->_getSession()->addError($message);
			            }
			        }
					$this->_redirect('customer/products/myproductslist/');
				}
			}
	}
	public function editvirtualPostAction() {
			$id = $this->getRequest()->getParam('productid');
			if ( $id !== false ) {
				list($data, $errors) = $this->validatePost();
				if ( !empty($errors) ) {
		        	foreach ($errors as $message) {
		                $this->_getSession()->addError($message);
		            }
					$this->_redirect('customer/products/edit/', array(
			                'id'    => $id
			            ));
				} else {
					$customerId = $this->_getSession()->getCustomer()->getid();
					/****** here i code for mysql prefix *****/
					$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
					$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
					/****** end here i code for mysql prefix *****/
					$product = Mage::getResourceModel('customerpartner/customerpartner_product_collection')
								->addAttributeToSelect('*')
								->addAttributeToFilter('customer_id', $customerId)
								->addAttributeToFilter('entity_id', $id)
								->load()
								->getFirstItem();
					$product->setName($this->getRequest()->getParam('name'));
					$product->setDescription($this->getRequest()->getParam('description'));
					$product->setShortDescription($this->getRequest()->getParam('short_description'));
					$product->setPrice($this->getRequest()->getParam('price'));
                    $product->setHighlights($this->getRequest()->getParam('highlight'));
                    $product->setFinePrint($this->getRequest()->getParam('fineprint'));
                    $product->setOriginalPrice($this->getRequest()->getParam('original_price'));
                    $product->setStartDate($this->getRequest()->getParam('start_date'));
                    $product->setEndDate($this->getRequest()->getParam('end_date'));
                    $product->setVoucherDate($this->getRequest()->getParam('voucher_date'));
                    //$product->setData('fine_print',$this->getRequest()->getParam('description'));
					$product->setWeight(0);
					$product->setStock($this->getRequest()->getParam('stock'));
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sku=$this->getRequest()->getParam('sku');
					$category=$this->getRequest()->getParam('category');
					/****** here i code for mysql prefix checking and execute code*****/
					$querydata=$write->query("update ".$mysqlprefix."customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
						$querydata=$write->query("update ".$mysqlprefix."customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					/** end **/
					#$querydata=$write->query("update customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
					#$querydata=$write->query("update customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					$product->save();
                    //print_r($product->getData());exit();

                    if ( $_FILES[images][name]!='' && count($_FILES) > 0 ) {
						if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$id)) {			
							foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$id) as $fileInfo) {
								if($fileInfo->isFile()){			
									unlink($fileInfo->getPathname());
								}
							}
						}
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
					        	if ( ( $error = $this->uploadImage($image, $id) ) !== true ) {
					        		$errors[] = $error;
					        	}
							}
						}
			        }
			        if ( empty($errors) ) {
			        	$this->_getSession()->addSuccess($this->__('Your product was successfully updated'));
			        } else {
			        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
			        	foreach ($errors as $message) {
			                $this->_getSession()->addError($message);
			            }
			        }
					$this->_redirect('customer/products/myproductslist/');
				}
			}
	}
	public function editPostAction() {
			$id = $this->getRequest()->getParam('productid');
			if ( $id !== false ) {
				list($data, $errors) = $this->validatePost();
				if ( !empty($errors) ) {
		        	foreach ($errors as $message) {
		                $this->_getSession()->addError($message);
		            }
					$this->_redirect('customer/products/edit/', array(
			                'id'    => $id
			            ));
				} else {
					$customerId = $this->_getSession()->getCustomer()->getid();
					/****** here i code for mysql prefix *****/
					$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
					$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
					/****** end here i code for mysql prefix *****/
					$product = Mage::getResourceModel('customerpartner/customerpartner_product_collection')
								->addAttributeToSelect('*')
								->addAttributeToFilter('customer_id', $customerId)
								->addAttributeToFilter('entity_id', $id)
								->load()
								->getFirstItem();
					$product->setName($this->getRequest()->getParam('name'));
					$product->setDescription($this->getRequest()->getParam('description'));
					$product->setShortDescription($this->getRequest()->getParam('short_description'));
					$product->setPrice($this->getRequest()->getParam('price'));
					$product->setWeight($this->getRequest()->getParam('weight'));
					$product->setStock($this->getRequest()->getParam('stock'));
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sku=$this->getRequest()->getParam('sku');
					$category=$this->getRequest()->getParam('category');
					/****** here i code for mysql prefix checking and execute code*****/
					$querydata=$write->query("update ".$mysqlprefix."customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
						$querydata=$write->query("update ".$mysqlprefix."customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					/** end **/
					#$querydata=$write->query("update customerpartner_entity_data set sku='".$sku."' where customerpartnerproductid='".$id."'");
					#$querydata=$write->query("update customerpartner_entity_data set category='".$category."' where customerpartnerproductid='".$id."'");
					$product->save();
					if ( $_FILES[images][name]!='' && count($_FILES) > 0 ) {
						if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$id)) {			
							foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$id) as $fileInfo) {
								if($fileInfo->isFile()){			
									unlink($fileInfo->getPathname());
								}
							}
						}
						foreach($_FILES as $image ) {
							if ( $image['tmp_name'] != '' ) {
					        	if ( ( $error = $this->uploadImage($image, $id) ) !== true ) {
					        		$errors[] = $error;
					        	}
							}
						}
			        }
			        if ( empty($errors) ) {
			        	$this->_getSession()->addSuccess($this->__('Your product was successfully updated'));
			        } else {
			        	$this->_getSession()->addError('Product info was saved but was imposible to save the image');
			        	foreach ($errors as $message) {
			                $this->_getSession()->addError($message);
			            }
			        }
					$this->_redirect('customer/products/myproductslist/');
				}
			}
	}
	private function validatePost() {
		$errors = array();
		$data = array();
		foreach( $this->getRequest()->getParams() as $code => $value ) {
			switch ($code) :
				case 'name':
					if (trim($value) == '' ) {
						$errors[] = 'Name has to be completed';
					} else {
						$data[$code] = $value;
					}
				break;
				case 'description':
					if (trim($value) == '' ) {
						$errors[] = 'Description has to be completed';
					} else {
						$data[$code] = $value;
					}
				break;
				case 'short_description':
					if (trim($value) == '' ) {
						$errors[] = 'Short description has to be completed';
					} else {
						$data[$code] = $value;
					}
				break;
                case 'highlight':
                    if (trim($value) == '' ) {
                        $errors[] = 'HighLights has to be completed';
                    } else {
                        $data[$code] = $value;
                    }
                break;
                case 'fineprint':
                    if (trim($value) == '' ) {
                        $errors[] = 'Fineprint has to be completed';
                    } else {
                        $data[$code] = $value;
                    }
                break;
                case 'start_date':
                    if (trim($value) == '' ) {
                        $errors[] = 'Start Date has to be completed';
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'end_date':
                    if (trim($value) == '' ) {
                        $errors[] = 'End Date has to be completed';
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'voucher_date':
                    if (trim($value) == '' ) {
                        $errors[] = 'Voucher Validity has to be completed';
                    } else {
                        $data[$code] = $value;
                    }
                    break;
				case 'price':
					if ( !preg_match("/^([0-9])+?[0-9.]*$/",$value) ) {
						$errors[] = 'Price should contain only decimal numbers';
					} else {
						$data[$code] = $value;
					}
                case 'original_price':
                    if ( !preg_match("/^([0-9])+?[0-9.]*$/",$value) ) {
                        $errors[] = 'Original Price should contain only decimal numbers';
                    } else {
                        $data[$code] = $value;
                    }
				case 'weight':
					if ( !preg_match("/^([0-9])+?[0-9.]*$/",$value) ) {
						$errors[] = 'Weight should contain only decimal numbers';
					} else {
						$data[$code] = $value;
					}
				break;
				case 'stock':
					if ( !preg_match("/^([0-9])+$/",$value) ) {
						$errors[] = 'Product stock should contain only an integer number';
					} else {
						$data[$code] = $value;
					}
				break;
			endswitch;
		}

		return array($data, $errors);
	}
	private function uploadZipSample($image, $customer_partner_product_id) {
		$max_size = 3670016; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadzipsample');
		
		if (!is_dir(Mage::getBaseDir().'/media/customersproducts/')) {
			mkdir(Mage::getBaseDir().'/media/customersproducts/', 0755);
		}
		$my_upload->upload_dir = Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id."/"; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".gif", ".jpg", ".jpeg",".png"); // specify the allowed extensions here
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->the_temp_file = $image['tmp_name'];
		$my_upload->the_file = $image['name'];
		$my_upload->http_error = $image['error'];
		$my_upload->replace = true;  // because only a checked checkboxes is true
		$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "y"; // use this boolean to check for a valid filename
		$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";
		if ($my_upload->upload($new_name)) { // new name is an additional filename information, use this to rename the uploaded file
			return true;			
		} else {
			return $my_upload->show_error_string();			
		}
	}
	
	private function uploadImage($image, $customer_partner_product_id) {
		$max_size = 3670016; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadimage');
		if (!is_dir(Mage::getBaseDir().'/media/customersproducts/')) {
			mkdir(Mage::getBaseDir().'/media/customersproducts/', 0755);
		}
		$my_upload->upload_dir = Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id."/"; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".gif", ".jpg", ".jpeg",".png"); // specify the allowed extensions here
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->rename_file = false;
		#$my_upload->filename =	$image_name;
		$my_upload->the_temp_file = $image['tmp_name'];
		$my_upload->the_file = $image['name'];
		$my_upload->http_error = $image['error'];
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
	private function uploadZip($image, $customer_partner_product_id) {
		$max_size = "1113670016000"; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadzip');
		/* here is made chnage for new file */
		/*if (is_dir(Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id)) {
			
			foreach (new DirectoryIterator(Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id) as $fileInfo) {
    		if($fileInfo->isFile()){			
				unlink($fileInfo->getPathname());
    		}
			}
		}*/
		/* end here is made chnage for new file */
		if (!is_dir(Mage::getBaseDir().'/media/customersproducts/')) {
			mkdir(Mage::getBaseDir().'/media/customersproducts/', 0755);
		}
		$my_upload->upload_dir = Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id."/"; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".zip", ".ap", ".psd",".rar",".pdf"); // specify the allowed extensions here		
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->the_temp_file = $image['tmp_name'];
		$my_upload->the_file = $image['name'];
		$my_upload->http_error = $image['error'];
		$my_upload->replace = true;  // because only a checked checkboxes is true
		$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "y"; // use this boolean to check for a valid filename
		$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";
		if ($my_upload->upload($new_name)) { // new name is an additional filename information, use this to rename the uploaded file
			return true;
		} else {
			return $my_upload->show_error_string();
		}
		}
	private function uploadImage1($image, $customer_partner_product_id) {
		$max_size = 3670016; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadimage');
		if (!is_dir(Mage::getBaseDir().'/media/customersproducts/')) {
			mkdir(Mage::getBaseDir().'/media/customersproducts/', 0755);
		}
		$my_upload->upload_dir = Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id."/"; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".gif", ".jpg", ".jpeg",".png"); // specify the allowed extensions here
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->rename_file = false;
		#$my_upload->filename =	$image_name;
		$my_upload->the_temp_file = $image['tmp_name'];
		$my_upload->the_file = $image['name'];
		$my_upload->http_error = $image['error'];
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
	private function uploadZipSample1($image, $customer_partner_product_id) {
		$max_size = 3670016; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadzipsample');
		if (!is_dir(Mage::getBaseDir().'/media/customersproducts/')) {
			mkdir(Mage::getBaseDir().'/media/customersproducts/', 0755);
		}
		$my_upload->upload_dir = Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id."/"; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".gif", ".jpg", ".jpeg",".png"); // specify the allowed extensions here
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->the_temp_file = $image['tmp_name'];
		$my_upload->the_file = $image['name'];
		$my_upload->http_error = $image['error'];
		$my_upload->replace = true;  // because only a checked checkboxes is true
		$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "y"; // use this boolean to check for a valid filename
		$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";
		if ($my_upload->upload($new_name)) { // new name is an additional filename information, use this to rename the uploaded file
			return true;			
		} else {
			return $my_upload->show_error_string();			
		}
	}
	private function uploadZip1($image, $customer_partner_product_id) {
		$max_size = "1113670016000"; // the max. size for uploading
		$my_upload = Mage::getModel('customerprofile/uploadzip');
		if (!is_dir(Mage::getBaseDir().'/media/customersproducts/')) {
			mkdir(Mage::getBaseDir().'/media/customersproducts/', 0755);
		}
		$my_upload->upload_dir = Mage::getBaseDir().'/media/customersproducts/'.$customer_partner_product_id."/"; // "files" is the folder for the uploaded files
		$my_upload->extensions = array(".zip", ".ap", ".psd",".rar",".pdf"); // specify the allowed extensions here
		$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
		$my_upload->the_temp_file = $image['tmp_name'];
		$my_upload->the_file = $image['name'];
		$my_upload->http_error = $image['error'];
		$my_upload->replace = true;  // because only a checked checkboxes is true
		$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "y"; // use this boolean to check for a valid filename
		$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";
		if ($my_upload->upload($new_name)) { // new name is an additional filename information, use this to rename the uploaded file
			return true;
		} else {
			return $my_upload->show_error_string();
		}
		}
}
