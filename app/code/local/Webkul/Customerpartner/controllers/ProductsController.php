<?php

class Webkul_Customerpartner_ProductsController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		//$this->ltp();die;
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();
        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customerpartner');
        /**
         * Append freebidspromo block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('customerpartner/products', 'products')
        );
        $this->renderLayout();
	}
		
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('customerpartner/products_grid')->toHtml());
    }

	public function ltp()
	{
        $data = array();
		$data['name'] = 'SI!';
		$data['description'] = 'ASDAasdsad asd ad .arcu quam commodo nisl, vel tincidunt massa orci eget nisi. Quisque vehicula enim. Praesent sodales lectus eget lectus. Duis vel l';
		$data['short_description'] = 'Qasdasdmsa oinadfi dns. Nam nisl tellus, dapibus in; elementum ac, sollicitudin sed';
		$data['price'] = 20;
		$data['stock'] = 45;
		$data['weight'] = 0.5;
		$data['customer_id'] = 1;
        Mage::getModel('customerpartner/customerpartner')->createPartnerProduct($data);
	}

	/**
	 * Method to approve a customer uploaded product.
	 * Basically, creates a Magento Store product with the data entered by the Customer.
	 *
	 *
	 */
	public function approveAction()
	{
		$id = (int)$this->getRequest()->getParam('id');
  		//If we do not have the ID of the product, redirect GRID
		if(!$id){
			$this->_redirectReferer();
		}
		try{
			//Load the customer product
			/****** here i code for mysql prefix *****/
			$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
			$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
			/****** end here i code for mysql prefix *****/
			$customerProduct = Mage::getModel('customerpartner/customerpartner_product')->load($id);
			//Magento Product
			$magentoProductModel = Mage::getModel('catalog/product');
			#$magentoProductModel->setWebsiteIds(array(1));
			#$magentoProductModel->setWebsiteIds(Mage::getModel('core/website')->getCollection()->getAllIds());
			#$magentoProductModel->setWebsiteIds(array(Mage::getModel('core/website')->getCollection()->getAllIds()));
		    $magentoProductModel->setAttributeSetId($attributeSetId);
			#$magentoProductModel->setTypeId('simple');
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			/****** here i code for mysql prefix checking and execute code*****/
			$readresult=$write->query("SELECT attribute_set_id FROM ".$mysqlprefix."eav_attribute_set WHERE attribute_set_name='Default' AND entity_type_id=(SELECT entity_type_id FROM ".$mysqlprefix."eav_entity_type WHERE entity_type_code='catalog_product')");
			/** end **/
			#$readresult=$write->query("SELECT attribute_set_id FROM eav_attribute_set WHERE attribute_set_name='Default' AND entity_type_id=(SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code='catalog_product')");
			$row = $readresult->fetch();
			$magentoProductModel->setAttributeSetId($row['attribute_set_id']);
		    $magentoProductModel->setName($customerProduct->getName());
		    $magentoProductModel->setDescription($customerProduct->getDescription());
		    $magentoProductModel->setPrice($customerProduct->getPrice());
		    $magentoProductModel->setShortDescription($customerProduct->getShortDescription());
		    $magentoProductModel->setWeight($customerProduct->getWeight());
			$magentoProductModel->setStatus(1);
			$magentoProductModel->setTaxClassId('None');
			/****** here i code for mysql prefix checking and execute code*****/
			$querydata=$write->query("SELECT * FROM ".$mysqlprefix."customerpartner_entity_data where customerpartnerproductid='".$id."'");
			/** end **/
		    #$querydata=$write->query("SELECT * FROM customerpartner_entity_data where customerpartnerproductid='".$id."'");
			$rowdata=$querydata->fetch();
			$magentoProductModel->setTypeId($rowdata['producttype']);
			$magentoProductModel->setWebsiteIds(array($rowdata['wstoreids']));
			$magentoProductModel->setCategoryIds($rowdata['category']);
			$magentoProductModel->setSku($rowdata['sku']);			
			$saved = $magentoProductModel->save();
			$lastId = $saved->getId();
			$this->_addImages($lastId, $id);
			//Magento Stock
		    $this->_saveStock($lastId, $customerProduct);
			//Associatte Customers product with recently created Magento Product
			$customerProduct->setProductId($lastId);
			/* here the product id will be save to my db*/
			/****** here i code for mysql prefix checking and execute code*****/
			$write->query("update ".$mysqlprefix."customerpartner_entity_data set mageproductid ='".$lastId."' where customerpartnerproductid='".$id."'");
			/** end **/
			#$write->query("update customerpartner_entity_data set mageproductid ='".$lastId."' where customerpartnerproductid='".$id."'");
			/* end  */
			$customerProduct->save();
			/*************start mailing**************/
			//$userid=$rowdata['userid'];
			$catagory_model = Mage::getModel('catalog/category');
			$categoriesy = $catagory_model->load($rowdata['category']);
			$categoryname=$categoriesy->getName();
			$partnerdata=$write->query("SELECT userid FROM ".$mysqlprefix."customerpartner_entity_data where customerpartnerproductid='".$id."'");
			$rowpartnerdata=$partnerdata->fetch();
			$userdata=$write->query("SELECT * FROM ".$mysqlprefix." customer_entity_varchar where attribute_id=5 and entity_id='".$rowpartnerdata['userid']."'");
			$rowuserdata=$userdata->fetch();
			$userdatat=$write->query("SELECT * FROM ".$mysqlprefix." customer_entity_varchar where attribute_id=7 and entity_id='".$rowpartnerdata['userid']."'");
			$rowuserdatat=$userdatat->fetch();
			$cfname='Administrator';
			$cmail='administrator@store.com';
			$partneremail=$write->query("SELECT email FROM ".$mysqlprefix."customer_entity where entity_id='".$rowdata['userid']."'");
			$mailaddress=$partneremail->fetch();
			$m=$mailaddress['email'];
			$headers = "From: STORE";
			mail($mailaddress['email'],"Product Approved","Your Product is been approved by administrator of the store",$headers);
			$emailTemp = Mage::getModel('core/email_template')->loadDefault('whenproductapproved');
			$emailTempVariables = array();
			$emailTempVariables['myvar1'] = $customerProduct->getName();
			$emailTempVariables['myvar2'] =$customerProduct->getDescription();
			$emailTempVariables['myvar3'] =$customerProduct->getPrice();
			$emailTempVariables['myvar4'] =$categoryname;
			$emailTempVariables['myvar5'] =$rowuserdata['value'].' '.$rowuserdatat['value'];
			$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
			$emailTemp->setSenderName($cfname);
			$emailTemp->setSenderEmail($cmail);
			$emailTemp->send($m,$rowuserdata['value'].' '.$rowuserdatat['value'],$emailTempVariables);
		/*************end mail**************/
			Mage::getSingleton('adminhtml/session')->addSuccess('Product successfully approved.');
			$this->_redirect('adminhtml/catalog_product/edit', array(
               'id'    => $lastId,
               '_current'=>true
           ));
		}catch(Exception $e){
			$saved = $magentoProductModel->save();
			$lastId = $saved->getId();
			$this->_addImages($lastId, $id);
			//Magento Stock
		    $this->_saveStock($lastId, $customerProduct);
			//Associatte Customers product with recently created Magento Product
			$customerProduct->setProductId($lastId);
			/* here the product id will be save to my db*/
			/****** here i code for mysql prefix checking and execute code*****/
			$write->query("update ".$mysqlprefix."customerpartner_entity_data set mageproductid ='".$lastId."' where customerpartnerproductid='".$id."'");
			/** end **/
			#$write->query("update customerpartner_entity_data set mageproductid ='".$lastId."' where customerpartnerproductid='".$id."'");
			/* end  */
			$customerProduct->save();
			Mage::getSingleton('adminhtml/session')->addSuccess('Product successfully approved.');
			$this->_redirect('adminhtml/catalog_product/edit', array(
               'id'    => $lastId,
               '_current'=>true
            ));
		}
	}
	private function _addImages($objProduct, $customerProductId)
	{
		$mediDir = Mage::getBaseDir('media');
		$imagesdir = $mediDir . '/customersproducts/' . $customerProductId . '/';
		if(!file_exists($imagesdir)){
			return false;
		}
		foreach (new DirectoryIterator($imagesdir) as $fileInfo) {
    		if($fileInfo->isDot() || $fileInfo->isDir()) continue;
    		if($fileInfo->isFile()){
									$path_parts = pathinfo($fileInfo->getPathname());
									$fileext= $path_parts['extension'];
									$fileinfo=explode('@',$fileInfo->getPathname());
									if($fileinfo[1]=='300')
										{
										$removeflag=0;
										$addimageflag=1;
										$filename=$imagename."-".$objProduct."-X300px".".".$fileext;
										$price="1";										
										}
									if($fileinfo[1]=='800')
										{
										$addimageflag=0;
										$removeflag=1;
										$filename=$imagename."-".$objProduct."-X800px".".".$fileext;
										$price="2";										
										}
									if($fileinfo[1]=='')
										{
										$removeflag=1;
										$addimageflag=0;
										$filename=$imagename."-".$objProduct."-Xorginal".".".$fileext;
										$price="3";										
										}
									$linkModel = Mage::getModel('downloadable/link')
												 ->setData($fileInfo->getPathname())
												 ->setLinkType("file")
												->setProductId($objProduct)
												->setStoreId(0)
												->setWebsiteId(array(Mage::app()->getStore()->getId()));
									$linkModel->setprice($price);
									$linkModel->settitle("Image-".$filename);
									$linkModel->setNumberOfDownloads(0);
									$time=time().$filename;
									$newfile=Mage::getBaseDir('media').'/downloadable/files/links/'.$time;
									$cmd="cp ".$fileInfo->getPathname()." '".$newfile."'";
									exec($cmd);
									if($removeflag!=0)
										{
											$cmd2="rm -rf ".$fileInfo->getPathname();
											exec($cmd2);
										}											
									$linkModel->setLinkFile("/".$time);
									$linkModel->save();
									if($addimageflag==1)
										{ 
											$objprod=Mage::getModel('catalog/product')->load($objProduct);
											$objprod->addImageToMediaGallery($fileInfo->getPathname(), array ('image','small_image','thumbnail'), true, false);
											$objprod->save();					
										}
    		}
		}
	}
	private function _saveStock($lastId, $objProduct)
	{
			$stockItem = Mage::getModel('cataloginventory/stock_item');
		    $stockItem->loadByProduct($lastId);

		    if (!$stockItem->getId()) {
		        $stockItem->setProductId($lastId)->setStockId(1);
		    }
		    $stockItem->setData('is_in_stock', 1);
		    $savedStock = $stockItem->save();
		    $stockItem->load($savedStock->getId())->setQty($objProduct->getStock())->save();
	}
	/*
	 * Clear a product owed amount
	 */
	public function clearoAction()
	{
		$id = (int)$this->getRequest()->getParam('id');
		if(!$id){
			$this->$this->_redirectReferer();
		}
		try{
			/****** here i code for mysql prefix checking and execute code*****/
			$query = sprintf('UPDATE "'.$mysqlprefix.'"customerpartner_aux SET amount_owed = 0, cleared_at = NOW() WHERE product_id = %d AND amount_owed > 0', $id);
			/** end **/
			#$query = sprintf('UPDATE customerpartner_aux SET amount_owed = 0, cleared_at = NOW() WHERE product_id = %d AND amount_owed > 0', $id);
			Mage::getModel('customerpartner/customerpartner')->customQuery($query, 'write');

			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Amount owed successfully cleared.'));
			$this->_redirectReferer();
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirectReferer();
		}
	}
}
