<?php

/**
 * Class Web_Deal_Block_Catalog_Product_View
 * @property int $ordered_qty
 * @property string $highlights
 * @property string $fineprint
 */
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
        $this->ordered_qty = (int)$_productCollection->ordered_qty;
    }
    public function getPriceCurrency($price = 0)
    {
        //return Mage::helper('core')->currency($price, true);
        return Mage::app()->getStore()->getCurrentCurrency()->formatPrecision($price,0);
    }
    public function getParentCat()
    {
        $catIds = $this->getProduct()->getCategoryIds();
        if(!count($catIds))
        {
            return false;
        }
        $_cat = Mage::getModel('catalog/category')->getCollection()
            ->addFieldToFilter('entity_id',array('in'=>$catIds))
            ->addFieldToFilter('level','3')
            ->getFirstItem();
        if($_cat)
        {
            return Mage::getModel('catalog/category')->load($_cat->getId());
        }
        return false;
    }


}