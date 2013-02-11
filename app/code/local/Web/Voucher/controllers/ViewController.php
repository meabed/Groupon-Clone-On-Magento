<?php
class Web_Voucher_ViewController extends Mage_Core_Controller_Front_Action
{
    protected $_cookieCheckActions = array('downloadadmin');

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'downloadadmin',
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    public function indexAction()
    {
        if ($this->_loadValidVoucher()) {
            $this->loadLayout();
            $this->renderLayout();
        }

    }

    protected function _loadValidVoucher($voucherCode = null)
    {
        if (null === $voucherCode) {
            $voucherCode = $this->getRequest()->getParam('code');
        }
        if (!$voucherCode) {
            $this->_forward('noRoute');
            return false;
        }

        $voucherId = Mage::getModel('voucher/vouchers')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('deal_voucher_code', $voucherCode)
            ->setPageSize(1)
            ->getFirstItem()
            ->getId();
        $voucher = Mage::getModel('voucher/vouchers')->load($voucherId);
        if ($this->_canViewVoucher($voucher)) {
            $order = Mage::getModel('sales/order')->load($voucher->getOrderId());
            $product = Mage::getModel('catalog/product')->load($voucher->getProductId());
            Mage::register('current_voucher', $voucher);
            Mage::register('current_order', $order);
            Mage::register('current_product', $product);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    public function downloadAdminAction()
    {
        $voucherCode = $this->getRequest()->getParam('code');
        $auth = strtoupper($this->getRequest()->getParam('auth'));
        $cS = '';
        $voucherId = Mage::getModel('voucher/vouchers')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('deal_voucher_code', $voucherCode)
            ->setPageSize(1)
            ->getFirstItem()
            ->getId();
        $voucher = Mage::getModel('voucher/vouchers')->load($voucherId);
        if($voucher)
        {
            $cS = strtoupper(md5(strtoupper($voucher->getDealVoucherCode()).'213@#$%^$DFSfwer@!#'.$voucher->getOrderId()));
        }
        if($cS == $auth)
        {
            $order = Mage::getModel('sales/order')->load($voucher->getOrderId());
            $product = Mage::getModel('catalog/product')->load($voucher->getProductId());
            Mage::register('current_voucher', $voucher);
            Mage::register('current_order', $order);
            Mage::register('current_product', $product);
        }
        $this->loadLayout();
        $c = $this->getLayout()->getOutput();
        $mediaDir = Mage::getBaseDir('media').DS.'vouchers'.DS;
        $fname = $mediaDir.$auth.'.html';
        if(!file_exists($fname))
        {
            $rs = file_put_contents($mediaDir.$auth.'.html',$c);
            if($rs)
            {
                echo $fname;exit();
            }else{
                return ;
            }
        }
        echo $fname;exit();
    }

    protected function _canViewVoucher($voucher)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $storeId = Mage::app()->getStore()->getId();
        if ($voucher->getId() && $voucher->getCustomerId() && ($voucher->getCustomerId() == $customerId)
            && ($voucher->getStoreId() == $storeId)
        ) {
            return true;
        }
        return false;
    }

    public function downloadAction()
    {
        if ($this->_loadValidVoucher()) {
            $voucherCode = $this->getRequest()->getParam('code');
            $text = Mage::helper('voucher');
            $_product = Mage::registry('current_product');
            $pdf = new Zend_Pdf();
            $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER);
            $pageHeight = $page->getHeight();
            $pageWidth = $page->getWidth();
            //$page->rotate(($pageWidth/2), ($pageHeight/2), 1);
            $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
            $logoImage = Zend_Pdf_Image::imageWithPath(Mage::getDesign()->getSkinBaseDir() . '/images/logo_small_en.jpg');
            $footerImage = Zend_Pdf_Image::imageWithPath(Mage::getDesign()->getSkinBaseDir() . '/images/voucher_footer_en.png');
            $productImage = Zend_Pdf_Image::imageWithPath(Mage::getBaseDir() . '/media/catalog/product' . $_product->getVoucherImage());
            $footerImageHeight = $footerImage->getPixelHeight();
            $footerImageWidth = $footerImage->getPixelWidth();
            $logoImageHeight = 47;
            $logoImageWidth = 316;
            $tableWidth = 568;
            $startPoint = ($pageWidth - $tableWidth) / 2;
            $endPoint = $startPoint + $tableWidth;
            $botPoint = 10;
            $topPoint = $pageHeight - 30;
            $page->setLineWidth('0.3')
                ->setLineDashingPattern(array(3,
                3,
                3,
                3
            ))
                ->drawLine($startPoint, $topPoint, $startPoint, $botPoint)
                ->drawLine($endPoint, $topPoint, $endPoint, $botPoint)
                ->drawLine($startPoint, $topPoint, $endPoint, $topPoint)
                ->drawLine($startPoint, $botPoint, $endPoint, $botPoint)
                ->drawLine($startPoint, $pageHeight - $logoImageHeight - 235, $endPoint, $pageHeight - $logoImageHeight - 235)
                ->drawLine($startPoint, $pageHeight - $logoImageHeight - 235 - 325, $endPoint, $pageHeight - $logoImageHeight - 235 - 325);

            $page->setFillColor(Zend_Pdf_Color_Html::color('#16599D'))
                ->drawRectangle($startPoint + 2, $topPoint - $logoImageHeight - 2, $endPoint, $topPoint);

            $page->drawImage($logoImage, $startPoint, $topPoint - $logoImageHeight - 1, $startPoint + $logoImageWidth, $topPoint);
            $page->drawImage($footerImage, $startPoint + 2, $botPoint, $startPoint + $footerImageWidth - 20, $botPoint + $footerImageHeight);

            $page->drawImage($productImage, $startPoint + 7, $topPoint - 55 - $productImage->getPixelHeight(), $startPoint + 7 + 246, $topPoint - 55 - $productImage->getPixelHeight() + 165);

            $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'))
                ->setLineDashingPattern(array(1,
                0,
                1,
                0
            ))
                ->drawRectangle(($endPoint - 205), ($topPoint - 10), ($endPoint - 15), ($topPoint + 10))
                ->setLineDashingPattern(array(0,
                1000,
                0,
                1000
            ))
                ->setFillColor(Zend_Pdf_Color_Html::color('#EDF4FA'))
                ->drawRectangle($startPoint + 0.3, $pageHeight - $logoImageHeight - 235, $endPoint, $pageHeight - $logoImageHeight - 235 - 325);
            $style = new Zend_Pdf_Style();
            $style->setFont($font, 15);
            $page->setFont($font, 12)
                ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))
                ->drawText($text->__('Voucher Code: ' . $voucherCode), ($endPoint - 193), ($topPoint - 4))
                ->setFont($font, 15);
            $lines = explode("\n", $this->getWrappedText($text->__('Voucher for ') . $_product->getName(), $style, 270));
            //var_dump($lines);
            foreach ($lines as $k => $line) {
                $page->drawText($line, ($startPoint + $productImage->getPixelWidth() + 20), ($topPoint - 70) - ($k * 20));
            }
            //


            $pdf->pages[0] = ($page);
            $pdf->save(Mage::getBaseDir() . '/media/vouchers/' . $voucherCode . '.pdf');

            $this->getResponse()
                ->clearHeaders()
                ->setHeader('content-type:', 'Application/pdf')
                ->setHeader('Content-Type', 'application/force-download')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $voucherCode . '.pdf"')
                ->setBody($pdf->render());


        }
    }

    protected function getWrappedText($string, Zend_Pdf_Style $style, $max_width)
    {
        $wrappedText = '';
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $word_count = count($words);
            $i = 0;
            $wrappedLine = '';
            while ($i < $word_count) {
                /* if adding a new word isn't wider than $max_width,
            we add the word */
                if ($this->widthForStringUsingFontSize($wrappedLine . ' ' . $words[$i]
                    , $style->getFont()
                    , $style->getFontSize()) < $max_width
                ) {
                    if (!empty($wrappedLine)) {
                        $wrappedLine .= ' ';
                    }
                    $wrappedLine .= $words[$i];
                } else {
                    $wrappedText .= $wrappedLine . "\n";
                    $wrappedLine = $words[$i];
                }
                $i++;
            }
            $wrappedText .= $wrappedLine . "\n";
        }
        return $wrappedText;
    }

    /**
     * found here, not sure of the author :
     * http://devzone.zend.com/article/2525-Zend_Pdf-tutorial#comments-2535
     */
    protected function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;
    }

    public function historyAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Vouchers'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
}