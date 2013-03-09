<?php
class Web_Voucher_Adminhtml_VoucherController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('sales/vouchers');
        //->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Voucher Manager'));
        return $this;
    }

    public function indexAction()
    {

        $this->_initAction();
        //$this->_addContent($this->getLayout()->createBlock('voucher/adminhtml_voucher'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $voucherId = $this->getRequest()->getParam('id');
        $voucherModel = Mage::getModel('voucher/vouchers')->load($voucherId);

        if ($voucherModel->getId() || $voucherId == 0) {

            Mage::register('voucher_data', $voucherModel);

            $this->loadLayout();

            $this->_setActiveMenu('sales/vouchers');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('voucher/adminhtml_voucher_edit'))
                    ->_addLeft($this->getLayout()->createBlock('voucher/adminhtml_voucher_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('voucher')->__('Voucher does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function exportCsvAction()
    {
        $fileName = 'vouchers.csv';

        $content = $this->getLayout()
                ->createBlock('voucher/adminhtml_voucher_grid')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);

    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $voucherModel = Mage::getModel('voucher/vouchers');

                $voucherModel->setId($this->getRequest()->getParam('id'))
                        ->setDealVoucherCode($postData['deal_voucher_code'])
                        ->setStatus($postData['status'])
                        ->setUpdatedAt(now())
                        ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setVoucherData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setVoucherData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    public function massSendAction()
    {
        $voucherIds = $this->getRequest()->getParam('voucher');
        if (!is_array($voucherIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                $voucherModel = Mage::getModel('voucher/vouchers');
                foreach ($voucherIds as $voucherId) {
                        $voucher = $voucherModel->load($voucherId);
                        Mage::getModel('voucher/observer')->_sendVoucherEmail($voucher);
                        //$voucher->setIsSent($voucher->getIsSent()+1)->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d vouchers(s) email(s) were successfully sent', count($voucherIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function massPrintAction()
    {
        $voucherIds = $this->getRequest()->getParam('voucher');
        if (!is_array($voucherIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                $voucherModel = Mage::getModel('voucher/vouchers');
                foreach ($voucherIds as $voucherId) {
                    $voucher = $voucherModel->load($voucherId);
                    $auth = strtoupper(md5(strtoupper($voucher->getDealVoucherCode()).'213@#$%^$DFSfwer@!#'.$voucher->getOrderId()));
                    $url = Mage::getUrl('voucher/view/downloadadmin',array('code'=>$voucher->getDealVoucherCode(),'auth'=> $auth));
                    $urlx = file_get_contents($url);
                    $urls[] = $urlx;
                   // Mage::getModel('voucher/observer')->_sendVoucherEmail($voucher);
                    //$voucher->setIsSent($voucher->getIsSent()+1)->save();
                }
                print_r($urls);
                $string = join(' ',array_unique($urls));
                $fname = Mage::getBaseDir('media').DS.'vouchers'.DS.md5($string).'.pdf';
                $cmd = Mage::getBaseDir('lib').DS.'wkhtmltopdf '.$string.' '.$fname;
                $r = exec($cmd);
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d vouchers(s) generated '.$cmd. ' -- ' .md5($string).'.pdf' , count($voucherIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $voucherIds = $this->getRequest()->getParam('voucher');
        if (!is_array($voucherIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($voucherIds as $voucherId) {
                    $web = Mage::getSingleton('voucher/vouchers')
                            ->load($voucherId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setUpdatedAt(now())
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($voucherIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
