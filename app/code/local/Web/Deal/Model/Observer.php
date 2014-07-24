<?php

class Web_Deal_Model_Observer
{
    public function disableAddToCart(Varien_Event_Observer $observer)
    {
        $productId = Mage::app()->getRequest()->getParam('product');
        if ($productId) {
            if (Mage::helper('deal')->isProductExpired($productId)) {
                Mage::throwException(
                    Mage::helper('deal')->__('Sorry, this deal has already expired')
                );
                return false;
            }
        }

    }

    public function renewDeal()
    {
        $fromDateTime = Mage::getModel('core/date')->date();
        $productIds = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('auto_renew ', array('eq' => 1))
            ->addAttributeToFilter('end_date', array('gteq' => $fromDateTime))
            ->getAllIds();
        $product = Mage::getModel('catalog/product')->getCollection();

        foreach ($productIds as $id) {
            $product->load($id);
            $date1 = date('U', strtotime($product->getEndDate()));
            $date2 = date('U', strtotime($product->getStartDate()));
            $diff = $date1 - $date2;
            $product->setStartDate($product->getEndDate());
            $product->setEndDate(date('Y-m-d 00:00:00', ($date1 + $diff)));
            $product->save();
        }
    }
}
