<?php
/**
 * @category   MagePsycho
 * @package    MagePsycho_Easypathhints
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Easypathhints_Block_System_Config_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
         $html = '<div style="background:url(\'http://www.magepsycho.com/_logo.png\') no-repeat scroll 15px center #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 200px;">
                    <h4>About MagePsycho</h4>
                    <p>A Professional Zend PHP5 Certified Developer / Freelancer with specialization in CMS + E-Commerce Solutions.<br />
                    View more extensions @ <a href="http://www.magentocommerce.com/magento-connect/developer/MagePsycho" target="_blank">MagentoConnect</a><br />
                    <a href="http://www.magepsycho.com/contacts" target="_blank">Request a Quote / Contact Us</a><br />
                    Skype me @ magentopycho<br />
					Email me @ <a href="mailto:info@magepsycho.com">info@magepsycho.com</a><br />
					Follow me on Twitter <a href="http://twitter.com/magepsycho" target="_blank">@magepsycho</a><br />
                    Visit my website:  <a href="http://www.magepsycho.com" target="_blank">www.magespycho.com</a></p>
                </div>';

        return $html;
    }
}
