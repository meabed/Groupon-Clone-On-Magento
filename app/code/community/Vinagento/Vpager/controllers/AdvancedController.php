<?php
require_once BP.'/app/code/core/Mage/CatalogSearch/controllers/AdvancedController.php';
class Vinagento_Vpager_AdvancedController extends Mage_CatalogSearch_AdvancedController
{
    public function resultAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $this->getResponse()->setBody($this->_getAdvancedSearchResult());
        }else{
            $this->loadLayout();
            try {
                Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
                $this->_redirectError(
                    Mage::getModel('core/url')
                        ->setQueryParams($this->getRequest()->getQuery())
                        ->getUrl('*/*/')
                );
            }
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        }
    }
    protected function _getAdvancedSearchResult(){
        $layout = $this->getLayout();
        $layout->getUpdate()->load('catalogsearch_advanced_result_ajax');
        try {
            Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
            $this->_redirectError(
                Mage::getModel('core/url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $layout->generateXml()->generateBlocks();
        $output = $layout->getOutput();
        Mage::log($output);
        return $output;
    }
}
