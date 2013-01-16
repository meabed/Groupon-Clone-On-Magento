<?php 
class Webkul_Customerpartner_Model_Observer
	{
		/*
		public function sendBirthayEmail()
		{
			this collection get all users which have birthday on today
			$customer = Mage::getModel("customer/customer")->getCollection();
			$customer->addFieldToFilter('dob', array('like' => '%'.date("m").'-'.date("d").' 00:00:00'));
			$customer->addNameToSelect();
			$items = $customer->getItems();
			 
			 
			 
			foreach($items as $item)
			{
			// send email or do something      
			}
	 
			return $this;
		}*/
		public function exportProducts()
		{	
			echo "VPS Export Products called!";
			Mage::Log("exportProducts called!");
	
			$stringData="cron job worked"
			$myFile = "testFile.txt";
			$fh = fopen($myFile, 'w');
			fwrite($fh, $stringData);
		
			$headers = "From: STORE";
			mail("ankit@webkul.com","cronjob","cron exec",$headers);

		}

	 
	}