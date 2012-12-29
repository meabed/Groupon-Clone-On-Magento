<?php
class Web_Deal_Block_Home extends Mage_Core_Block_Template
{

    protected $_products;
    protected $_productsCollection;
    public function _construct()
    {
        //echo "X";
        //echo $this->_products->count();
    }
    public function getProducts()
    {
        $catCollection = Mage::helper('deal')->getActiveCategories(null);
        $catIds = $catCollection->getAllIds();
        $this->_products = Mage::getModel('catalog/product')->getCollection()
            ->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
            ->addAttributeToSelect('*')
        //->addAttributeToFilter('main_deal',array('eq'=>'1'))
            ->addAttributeToFilter('start_date',array('to'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
            ->addAttributeToFilter('end_date',array('from'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
            ->addAttributeToSort('main_deal','DESC')
            ->addAttributeToSort('sort','ASC')
            ->addAttributeToSort('created_at','DESC')
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
            ->addAttributeToFilter('category_id', array('in' => $catIds));
        $idxs = array_unique($this->_products->getAllIds());
        $this->_products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id',array('in'=>$idxs))
            ->addAttributeToSort('main_deal','DESC')
            ->addAttributeToSort('sort','ASC')
            ->addAttributeToSort('created_at','DESC')
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