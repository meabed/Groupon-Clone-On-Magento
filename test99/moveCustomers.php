<?php
$_magePathNew = '../app/Mage.php';

$_magePath = '/home/dealgobb/dealgobbler.com/html/app/Mage.php';

include_once($_magePath);

//$dbh = new PDO('mysql:host=localhost;dbname=dealgobb_groupclone332', 'dealgobb_tariq', 'PimplyForeGeneraApathy');

Mage::app();

$customers = Mage::getModel('customer/customer')->getCollection();


echo $customers->getSize();
