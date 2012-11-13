<?php
class Web_Deal_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static function getFullProductUrl(Mage_Catalog_Model_Product $product,
                                             Mage_Catalog_Model_Category $category = null,
                                             $mustBeIncludedInNavigation = true)
    {
        $currentCategory = Mage::registry('current_category');
        $catIds = $product->getCategoryIds();
        $url = Mage::getModel('core/url_rewrite')->getCollection()
            ->addFilter('is_system', '1')
            ->addFilter('product_id', $product->getId())
            ->addFilter('store_id', Mage::app()->getStore()->getId())
            ->addFieldToFilter('category_id', array('gt' => '0'));

        if ($currentCategory && in_array($currentCategory->getId(), $catIds)) {
            $catId = $currentCategory->getId();
            $url->addFilter('category_id', $catId);
        }
        $rs = $url->setOrder('url_rewrite_id', 'DESC')->getFirstItem();
        if (!$rs->getRequestPath()) {
            return $product->getUrlPath();
        }
        return Mage::getBaseUrl() . $rs->getRequestPath();

    }

    public function getProductCategoryName(Mage_Catalog_Model_Product $product)
    {
        $productCategories = $product->getCategoryIds();

        if (!count($productCategories)) {
            return;
        }
        $tmpCategory = Mage::getModel('catalog/category')->load($productCategories[0]);
        //echo $tmpCategory->getId();
        return $tmpCategory->getName();
    }

    /**
     * Checks if a category matches criteria: active && url_key not null && included in menu if it has to
     */
    protected static function isCategoryAcceptable(Mage_Catalog_Model_Category $category = null, $mustBeIncludedInNavigation = true)
    {
        if (!$category->getIsActive() || is_null($category->getUrlKey())
            || ($mustBeIncludedInNavigation && !$category->getIncludeInMenu())
        ) {
            return false;
        }
        return true;
    }

    public function getPriceCurrency($price = 0)
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode() . ' ' . Mage::helper('core')->currency($price, false);
    }

    public function getDateDiff($date1, $date2)
    {
        $epoch_1 = strtotime($date1);
        $epoch_2 = strtotime($date2);

        $diff_seconds = $epoch_1 - $epoch_2;
        if ($diff_seconds <= 0) {
            return 0;
        }

        $s = ' Days';
        $diff = floor($diff_seconds / 86400); // days
        if ($diff == 0) {
            $s = ' Hours';
            $diff = floor($diff_seconds / 3600); // hours
            if ($diff == 0) {
                $s = ' Minutes';
                $diff = floor($diff_seconds / 60); // minutes
                if ($diff == 0) {
                    $s = ' Seconds';
                    $diff = $diff_seconds;
                }
            }
        }

        return $diff . $s;

    }
    public function getActiveCategories($mainCat = null)
    {
        if($mainCat == null)
        {
            $catCookie = Mage::getModel('core/cookie')->get('main_cat');
            $mainCat = empty($catCookie) ? (int)Mage::getStoreConfig('deal/config/main_cat') : Mage::getModel('core/cookie')->get('main_cat');
        }
        $cats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addFilter('parent_id', $mainCat)
            ->addFilter('level', '3')
            ->addAttributeToFilter('include_in_menu',array('eq'=>1))
            ->addAttributeToFilter('is_active',array('eq'=>1));
        return $cats;
    }

}
	 