<?php
class Vinagento_Vpager_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function addLnav($url){
		if(strpos($url, 'l=0')===false&&strpos($url, 'l=1'===false)){
			$url .= '&l=0';
		}
		return $url;
	}
	public function addLnav1($url){
		$search = array('&l=0','l=0','%26l%3d0');
		$url = str_replace($search,'',$url);
		if(strpos($url, 'l=1')===false){
			$url .= '&l=1';
		}
		return $url;
	}
	public function clearLnav($url){
		$search = array('&l=1','l=1','%26l%3d1','#%21l=1','%23!l%3d1','#!l=1');
		return str_replace($search,'',$url);
	}
}