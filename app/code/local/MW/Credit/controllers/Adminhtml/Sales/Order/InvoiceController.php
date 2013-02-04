<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php';
class MW_Credit_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController
{	

public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');
        try {
            if ($invoice = $this->_initInvoice()) {
            	
            	
				/**/
            	if(Mage::helper('credit')->getEnabled()) {
	            	// get virtual product enable credit into product
	            	$items = $invoice->getOrder()->getAllItems();
	            	$amountCredit = 0;
	                foreach ($items as $itemId => $item)
	                {
	                	$creditEnabled = Mage::getModel('catalog/product')->load($item->getProductId())
	                    											 ->getCreditEnabled();
	                	// only agree one virtual is add
	                	if ($item->getIsVirtual() && $creditEnabled ){
	                    	$amountCredit = $item->getPrice() * $item->getQtyToInvoice();
	                	} 
	                }
	                if($amountCredit != 0) {
		                $creditcustomer = Mage::getModel('credit/creditcustomer')
		                					->load($invoice->getOrder()->getCustomerId());
	    				$oldCredit = $creditcustomer->getCredit();
	    				$newCredit = $oldCredit + $amountCredit;
	    				// Save history transaction
		            	$historyData = array('type_transaction'=>MW_Credit_Model_TransactionType::BUY_CREDIT, 
		            					     'transaction_detail'=>$invoice->getOrder()->getData('increment_id'), 
		            						 'amount'=>$amountCredit, 
		            						 'beginning_transaction'=>$oldCredit,
		            						 'end_transaction'=>$newCredit,
		            					     'created_time'=>now());
		          
		            	Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData, $creditcustomer->getId());
		            	//Add credit of customer
		            	$creditcustomer->setCredit($newCredit)->save();
	                }
            	}
                /**/
            	
            	

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                if (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('Invoice and shipment have been successfully created.'));
                }
                else {
                    $this->_getSession()->addSuccess($this->__('Invoice has been successfully created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send shipment email.'));
                    }
                }
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            }
            else {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            }
            return;
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to save invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }
	
//    public function saveAction()
//    {
//        $data = $this->getRequest()->getPost('invoice');
//        $orderId = $this->getRequest()->getParam('order_id');
//        try {
//            if ($invoice = $this->_initInvoice()) {
//

//            	
//                if (!empty($data['capture_case'])) {
//                    $invoice->setRequestedCaptureCase($data['capture_case']);
//                }
//
//                if (!empty($data['comment_text'])) {
//                    $invoice->addComment($data['comment_text'], isset($data['comment_customer_notify']));
//                }
//
//                $invoice->register();
//
//                if (!empty($data['send_email'])) {
//                    $invoice->setEmailSent(true);
//                }
//
//                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
//                $invoice->getOrder()->setIsInProcess(true);
//
//                $transactionSave = Mage::getModel('core/resource_transaction')
//                    ->addObject($invoice)
//                    ->addObject($invoice->getOrder());
//                $shipment = false;
//                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
//                    $shipment = $this->_prepareShipment($invoice);
//                    if ($shipment) {
//                        $shipment->setEmailSent($invoice->getEmailSent());
//                        $transactionSave->addObject($shipment);
//                    }
//                }
//                $transactionSave->save();
//
//                if (!empty($data['do_shipment'])) {
//                    $this->_getSession()->addSuccess($this->__('Invoice and shipment have been successfully created.'));
//                }
//                else {
//                    $this->_getSession()->addSuccess($this->__('Invoice has been successfully created.'));
//                }
//
//                // send invoice/shipment emails
//                $comment = '';
//                if (isset($data['comment_customer_notify'])) {
//                    $comment = $data['comment_text'];
//                }
//                try {
//                    $invoice->sendEmail(!empty($data['send_email']), $comment);
//                } catch (Exception $e) {
//                    Mage::logException($e);
//                    $this->_getSession()->addError($this->__('Unable to send invoice email.'));
//                }
//                if ($shipment) {
//                    try {
//                        $shipment->sendEmail(!empty($data['send_email']));
//                    } catch (Exception $e) {
//                        Mage::logException($e);
//                        $this->_getSession()->addError($this->__('Unable to send shipment email.'));
//                    }
//                }
//                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
//            }
//            else {
//                $this->_redirect('*/*/new', array('order_id' => $orderId));
//            }
//            return;
//        }
//        catch (Mage_Core_Exception $e) {
//            $this->_getSession()->addError($e->getMessage());
//        }
//        catch (Exception $e) {
//            $this->_getSession()->addError($this->__('Failed to save invoice.'));
//            Mage::logException($e);
//        }
//        $this->_redirect('*/*/new', array('order_id' => $orderId));
//    }
}