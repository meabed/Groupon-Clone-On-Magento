<?php
class Web_Deal_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    protected $_info;
    public function getDealInfo()
    {
        $this->highlights = explode("\n",$this->getProduct()->getHighlights());
        $this->fineprint =  explode("\n",$this->getProduct()->getFinePrint());
        $_productCollection = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToFilter('entity_id', $this->getProduct()->getId())
            ->setOrder('ordered_qty', 'desc')
            ->getFirstItem();
        $this->ordered_qty = $_productCollection->ordered_qty;
    }
    public function getPriceCurrency($price = 0)
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode() . ' ' . Mage::helper('core')->currency($price, false);
    }
}