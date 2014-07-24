<?php

class Web_Deal_Model_System_Config_Source_Categories
{
    public function toOptionArray()
    {
        $array = array();
        $cats = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name')->addFilter('level', '2')->load();
        foreach ($cats as $cat) {
            $array[] = array('value' => $cat->getId(), 'label' => $cat->getName());
        }
        return $array;
    }
}
