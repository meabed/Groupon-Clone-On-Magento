<?php

class Webkul_Customerpartner_PartnersController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
	{

		//$this->ltp();die;

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customerpartner');

        /**
         * Append freebidspromo block to content
         */
        $this->_addContent(
            #$this->getLayout()->createBlock('customerpartner/products', 'products')
            $this->getLayout()->createBlock('customerpartner/partners', 'partners')
        );

        $this->renderLayout();
	}
		
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('customerpartner/partners_grid')->toHtml());
    }
	
	public function ltp()
	{

        $data = array();
		$data['name'] = 'SI!';
		$data['description'] = 'ASDAasdsad asd ad .arcu quam commodo nisl, vel tincidunt massa orci eget nisi. Quisque vehicula enim. Praesent sodales lectus eget lectus. Duis vel l';
		$data['short_description'] = 'Qasdasdmsa oinadfi dns. Nam nisl tellus, dapibus in; elementum ac, sollicitudin sed';
		$data['price'] = 20;
		$data['stock'] = 45;
		$data['weight'] = 0.5;
		$data['customer_id'] = 1;

        Mage::getModel('customerpartner/customerpartner')->createPartnerProduct($data);

	}
	/*****action of  is partner*****/
	public function massispartnerAction()
	{
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
		$wholedata=$this->getRequest()->getParams();
		#print_r($wholedata);
		$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
		foreach($wholedata['customer'] as $key)
		{	
			$querydata=$write->query("select * from ".$mysqlprefix."customerpartner_entity_userdata where mageuserid ='$key'");
			$row=$querydata->fetch();
				if(strlen($row['autoid'])==0)
				{	$customer = Mage::getModel('customer/customer')->load($key);
					$querydatainsert=$write->query("insert into  ".$mysqlprefix."customerpartner_entity_userdata values('','".$customer['firstname']."','".$customer['lastname']."','1','".$customer['email']."','$key')");
				}
				else{
					$querydataupdate=$write->query("update ".$mysqlprefix."customerpartner_entity_userdata set wantpartner ='1' where mageuserid ='$key'");
				}
		}
		$this->_getSession()->addSuccess($this->__('Your User was successfully saved'));
		$this->_redirect('customerpartner/partners/');
	}
	/*****end *****/
	/*****action of  is not partner*****/
	public function massnotpartnerAction()
	{	
		/****** here i code for mysql prefix *****/
		$mysqlprefix=Mage::getStoreConfig('partners/partners_options/mysqlprefix');
		$mysqlprefixlen=strlen(Mage::getStoreConfig('partners/partners_options/mysqlprefix'));
		/****** end here i code for mysql prefix *****/
		$wholedata=$this->getRequest()->getParams();
		$write = Mage::getSingleton('core/resource')->getConnection('core_write'); // for connectivity in write mode
		foreach($wholedata['customer'] as $key)
		{	
			$querydata=$write->query("select * from ".$mysqlprefix."customerpartner_entity_userdata where mageuserid ='$key'");
			$row=$querydata->fetch();
				if(strlen($row['autoid'])==0)
				{	$customer = Mage::getModel('customer/customer')->load($key);
					$querydatainsert=$write->query("insert into  ".$mysqlprefix."customerpartner_entity_userdata values('','".$customer['firstname']."','".$customer['lastname']."','0','".$customer['email']."','$key')");
				}
				else{
					$querydataupdate=$write->query("update ".$mysqlprefix."customerpartner_entity_userdata set wantpartner ='0' where mageuserid ='$key'");
				}
		}
		$this->_getSession()->addSuccess($this->__('Your User was successfully saved'));
		$this->_redirect('customerpartner/partners/');
	}
	
	

	
	

	

	


}
