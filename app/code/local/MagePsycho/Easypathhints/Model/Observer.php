<?php
/**
 * @category   MagePsycho
 * @package    MagePsycho_Easypathhints
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Easypathhints_Model_Observer {

	public function setTemplatePathHints(Varien_Event_Observer $observer) {
		$helper				= Mage::helper('easypathhints');
		$isActive			= $helper->isActive();
		$tp					= Mage::app()->getRequest()->getParam('tp');
		$accessCode			= Mage::app()->getRequest()->getParam('code');
		$dbAccessCode		= $helper->getConfig('code');

		if(!empty($dbAccessCode)){
			$checkAccessCode = ($dbAccessCode == $accessCode) ? true : false;
		}else{
			$checkAccessCode = true;
		}

		if ($tp && $isActive && $checkAccessCode):

			/* @var $block Mage_Core_Block_Abstract */
			$block				= $observer->getBlock();
			$transport			= $observer->getTransport();

			$fileName			= $block->getTemplateFile();
			$thisClass			= get_class($block);
			if($fileName){
				$preHtml = '<div style="position:relative; border:1px dotted red; margin:6px 2px; padding:18px 2px 2px 2px; zoom:1;">
	<div style="position:absolute; left:0; top:0; padding:2px 5px; background:red; color:white; font:normal 11px Arial;
	text-align:left !important; z-index:998;" onmouseover="this.style.zIndex=\'999\'"
	onmouseout="this.style.zIndex=\'998\'" title="'.$fileName.'">'.$fileName.'</div>';
				$preHtml .= '<div style="position:absolute; right:0; top:0; padding:2px 5px; background:red; color:blue; font:normal 11px Arial;
		text-align:left !important; z-index:998;" onmouseover="this.style.zIndex=\'999\'" onmouseout="this.style.zIndex=\'998\'"
		title="'.$thisClass.'">'.$thisClass.'</div>';

				$postHtml = '</div>';
			}else{
				$preHtml	= null;
				$postHtml	= null;
			}


			$html = $transport->getHtml();
			$html = $preHtml . $html . $postHtml;
			$transport->setHtml($html);

		endif;
	}
}