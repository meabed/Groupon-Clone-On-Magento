<?php

class Webkul_Customerprofile_Model_Gendertype extends Mage_Core_Model_Abstract
{


    public function toOptionArray()
    {
        return array(
            array('label'=>Mage::helper('customer')->__('Male'), 'value'=>'male'),
            array('label'=>Mage::helper('customer')->__('Female'), 'value'=>'female')
        );
    }

    public function getAllOptions()
    {
       return array(
            array('label'=>Mage::helper('customer')->__('Male'), 'value'=>'male'),
            array('label'=>Mage::helper('customer')->__('Female'), 'value'=>'female')
        );
    }


}
