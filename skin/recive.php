<?php
require '../app/Mage.php';
Mage::App('default');$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_POST['sku']); 
if((strlen($product['sku']>0))&&($product['sku']==$_POST['sku']))
 { echo strlen($product['sku']);}
 else {echo strlen($product['sku']);}
?>