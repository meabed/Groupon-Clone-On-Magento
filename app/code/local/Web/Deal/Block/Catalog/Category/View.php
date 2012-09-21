<?php
class Web_Deal_Block_Catalog_Category_View extends Mage_Catalog_Block_Category_View
{
    protected $_products;
    public function _construct()
    {
        //echo "X";
        //echo $this->_products->count();

    }
    public function getProducts()
    {
        $currentCategory = Mage::registry('current_category');

        $this->_products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*');
        if($currentCategory){
            $this->_products->addCategoryFilter($currentCategory);
        }
        //->addAttributeToFilter('main_deal',array('eq'=>'1'))

        $this->_products->addAttributeToFilter('start_date',array('to'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
            ->addAttributeToFilter('end_date',array('from'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
            ->addAttributeToSort('sort','ASC')
            ->addAttributeToSort('main_deal','DESC')
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
            ->load();
        $skus = array();
        foreach($this->_products as $product)
        {
            $skus[] = $product->getSku();
        }
        $qtyOrdered = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToFilter('sku', array('in'=>$skus))
            ->load();
        //var_dump($qtyOrdered->getData());
        //->setOrder('ordered_qty', 'desc')
        $qtyData = array();
        foreach($qtyOrdered as $qty)
        {
            $qtyData[$qty->getSku()] = (int) $qty->getOrderedQty();
        }
        return array('products'=>$this->_products,'qty'=>$qtyData);
    }
}