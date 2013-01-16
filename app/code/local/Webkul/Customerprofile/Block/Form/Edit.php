<?php

class Webkul_Customerprofile_Block_Form_Edit extends Mage_Customer_Block_Form_Edit {

    public function getGenderHtmlSelect($defValue=null, $name='gender', $id='gender', $title='Gender')
    {
    	if ( $this->getCustomer()->getGender() != '' ) {
			$defValue = $this->getCustomer()->getGender();
    	} else {
    		$defValue = ($defValue == null) ? 'male' : $defValue;
    	}

		$cacheKey = 'CUSTOMER_GENDER_SELECT_STORE_'.Mage::app()->getStore()->getCode();
		if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
		    $options = unserialize($cache);
		} else {
		    $options = Mage::getModel('customerprofile/gendertype')->toOptionArray();
		    if (Mage::app()->useCache('config')) {
		        Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
		    }
		}
        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName($name)
            ->setId($id)
            ->setTitle(Mage::helper('customer')->__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();

        return $html;
    }

    public function getAvatar($customerId, $w = 120, $h = 120)
    {

    	$customer = Mage::getModel('customer/customer')->load($customerId);

    	$name = '';
    	$base_dir = Mage::getBaseDir();

		if( @file_exists($base_dir.'/media/avatar/' . $customerId . '.jpg' ) ) {
			$name = $base_dir.'/media/avatar/' . $customerId . '.jpg';
			$small_name = $base_dir.'/media/avatar/' . $customerId . '_small.jpg';
		} elseif( @file_exists($base_dir.'/media/avatar/' . $customerId . '.jpeg' ) ) {
			$name = $base_dir.'/media/avatar/' . $customerId . '.jpeg';
			$small_name = $base_dir.'/media/avatar/' . $customerId . '_small.jpeg';
		} elseif( @file_exists($base_dir.'/media/avatar/' . $customerId . '.gif' ) ) {
			$name = $base_dir.'/media/avatar/' . $customerId . '.gif';
			$small_name = $base_dir.'/media/avatar/' . $customerId . '_small.gif';
		} elseif( @file_exists($base_dir.'/media/avatar/' . $customerId . '.png' ) ) {
			$name = $base_dir.'/media/avatar/' . $customerId . '.png';
			$small_name = $base_dir.'/media/avatar/' . $customerId . '_small.png';
		}

		if(isset($small_name)){
			$small_name = str_replace('_small', '_' . $w . '_' . $h . '_small', $small_name);
		}

		if ( $name != '' ) {
			$resize_object = Mage::getModel('customerprofile/resizeimage');
			$resize_object->setImage($name);
			if ( $resize_object->resize_limitwh($w, $h, $small_name) === false ) {
				return $resize_object->error();
			} else {
				return '<img src="'.str_replace('index.php', '', $this->getBaseUrl()).str_replace($base_dir, '', $small_name).'" alt="' . $customer->getNickname() . '" />';
			}
		} else {
			return '';
		}

    }

/*    public function getAvatar() {
    	$name = '';
    	$base_dir = Mage::getBaseDir();
		if( @file_exists($base_dir.'/media/avatar/'.$this->getCustomer()->getId().'.jpg' ) ) {
			$name = $base_dir.'/media/avatar/'.$this->getCustomer()->getId().'.jpg';
			$small_name = $base_dir.'/media/avatar/'.$this->getCustomer()->getId().'_small.jpg';
		} elseif( @file_exists($base_dir.'/media/avatar/'.$this->getCustomer()->getId().'.gif' ) ) {
			$name = $base_dir.'/media/avatar/'.$this->getCustomer()->getId().'.gif';
			$small_name = $base_dir.'/media/avatar/'.$this->getCustomer()->getId().'_small.gif';
		} elseif( @file_exists($base_dir.'/media/avatar/'.$this->getCustomer()->getId().'.png' ) ) {
			$name = $base_dir.'/media/avatar/'.$this->getCustomer()->getId().'.png';
			$small_name = $base_dir.'/media/avatar/'.$this->getCustomer()->getId().'_small.png';
		}

		if ( $name != '' ) {
			$resize_object = Mage::getModel('customerprofile/resizeimage');
			$resize_object->setImage($name);
			if ( $resize_object->resize_limitwh(120,120, $small_name) === false ) {
				return $resize_object->error();
			} else {
				return '<img src="'.str_replace($base_dir, '', $small_name).'" alt="'.$this->getCustomer()->getNickname().'" />';
			}
		} else {
			return '';
		}

    }*/

}
?>
