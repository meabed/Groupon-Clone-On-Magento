<?php
class Web_Deal_Block_Sidebar extends Mage_Core_Block_Template
{

    protected $_products;
    protected $_catproducts;
    protected $_currentProduct;
    protected $_category;
    protected $_productsCollection;
    public function _construct()
    {
        //echo $this->_products->count();
        $this->_category = Mage::registry('current_category');


    }
    public function getProducts($ex = array())
    {
        if($this->_category){
            $this->_catproductsIds = Mage::getModel('catalog/product')->getCollection()

                ->addAttributeToSelect('*')
              //->addAttributeToFilter('main_deal',array('eq'=>'1'))
                ->addAttributeToFilter('start_date',array('to'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
                ->addAttributeToFilter('end_date',array('from'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
                ->addAttributeToSort('sort','ASC')
                ->addAttributeToSort('main_deal','DESC')
                ->addCategoryFilter($this->_category)
                ->addAttributeToFilter('entity_id',array('nin'=>$ex))
                ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                ->getAllIds();
            $this->_catproducts = Mage::getModel('catalog/product')->getCollection()
                                    ->addFieldToFilter('entity_id',array('in'=>array_unique($this->_catproductsIds)))
                                    ->load();
        }



        /**
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
        }*/
        return array('products'=>$this->_catproducts);
    }
    public function getOtherProducts($ex = array())
    {
        $catCollection = Mage::helper('deal')->getActiveCategories(null);
        $catIds = $catCollection->getAllIds();

        $this->_productsIds = Mage::getModel('catalog/product')->getCollection()
            ->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
            ->addAttributeToSelect('*')
        //->addAttributeToFilter('main_deal',array('eq'=>'1'))
            ->addAttributeToFilter('start_date',array('to'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
            ->addAttributeToFilter('end_date',array('from'=>Mage::getModel('core/date')->date('Y-m-d H:i:s')))
            ->addAttributeToSort('sort','ASC')
            ->addAttributeToSort('main_deal','DESC')
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter('entity_id',array('nin'=>$ex))
            ->addAttributeToFilter('category_id', array('in' => $catIds))
            ->getAllIds();

        $this->_products = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id',array('in'=>array_unique($this->_productsIds)))
            ->load();
        return array('products'=>$this->_products);
    }
}