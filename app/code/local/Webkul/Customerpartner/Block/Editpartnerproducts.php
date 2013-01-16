<?php

class Webkul_Customerpartner_Block_Editpartnerproducts extends Mage_Customer_Block_Account_Dashboard
{
	protected $_product = null;

	public function getProduct() {

		if ( $this->_product == null ) {
			$id = $this->getRequest()->getParam('id');

			$customerId = $this->getCustomer()->getId();
			$products = Mage::getResourceModel('customerpartner/customerpartner_product_collection')
						->addAttributeToSelect('*')
						->addAttributeToFilter('customer_id', $customerId)
						->addAttributeToFilter('entity_id', $id)
						->load()
						->getFirstItem()
						->toArray();

			$this->_product = $products;
		}
		return $this->_product;
	}

	public function getImages() {
		$images = array();
		$imagesdir = Mage::getBaseDir() . '/media/customersproducts/' . $this->_product['entity_id'] . '/';

		if(!file_exists($imagesdir)){
			return $images;
		}

		if(!file_exists($imagesdir.'small/')){
			mkdir($imagesdir.'small/', 0755);
		}

		foreach (new DirectoryIterator($imagesdir) as $fileInfo) {

    		if($fileInfo->isDot()) continue;

    		if($fileInfo->isFile()){
    			$resize_object = Mage::getModel('customerprofile/resizeimage');
    			$resize_object->setImage($fileInfo->getPathname());

				$small_name = str_replace(".", "_small.", 'media/customersproducts/' . $this->_product['entity_id'] . '/small/' .$fileInfo->getFilename());

				if ( $resize_object->resize_limitwh(200, 200, $small_name) === false ) {
					$images[] = $resize_object->error();
				} else {
					$images[] = '<img src="'.Mage::getUrl().$small_name.'" />';
				}

    		}

		}

		return $images;
	}
}
?>
