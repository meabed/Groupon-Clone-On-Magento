<?php
/**
 * Created by Mohammed Meabed
 * Date: 7/18/13
 * Time: 3:46 AM
 */ 
class Web_States_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getLocales()
    {
        $stores = Mage::app()->getStores();
        $locales = array();
        foreach ($stores as $store) {
            $v = Mage::getStoreConfig('general/locale/code', $store->getId());
            $locales[$v] = $v;
        }
        return $locales;
    }

}