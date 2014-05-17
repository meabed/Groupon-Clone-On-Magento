<?php
class Web_States_Block_Adminhtml_States_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $countries = Mage::getSingleton('directory/country')->getCollection()->loadData()->toOptionArray(false);
        $id = $this->getRequest()->getParam('region_id');



        $fieldSet = $form->addFieldset('web_states_form', array('legend' => Mage::helper('web_states')->__('State information')));
        $fieldSet->addField(
            'country_id', 'select', array(
                                         'label'    => Mage::helper('web_states')->__('Country'),
                                         'name'     => 'country_id',
                                         'required' => true,
                                         'values'   => $countries
                                    )
        );

        $fieldSet->addField(
            'code', 'text', array(
                                 'label'    => Mage::helper('web_states')->__('Code'),
                                 'class'    => 'required-entry',
                                 'required' => true,
                                 'name'     => 'code',
                            )
        );
        $fieldSet->addField(
            'default_name', 'text', array(
                                         'label'    => Mage::helper('web_states')->__('Default Name'),
                                         'class'    => 'required-entry',
                                         'required' => true,
                                         'name'     => 'default_name',
                                    )
        );
        $locales = Mage::helper('web_states')->getLocales();
        foreach ($locales as $locale) {
            $fieldSet{$locale} = $form->addFieldset('web_states_form_' . $locale, array('legend' => Mage::helper('web_states')->__('Locale ' . $locale)));
            $fieldSet{$locale}->addField(
                'name_'.$locale, 'text', array(
                                     'label' => Mage::helper('web_states')->__('Name'),
                                     'name'  => 'name_'.$locale,
                                )
            );
        }
        if (Mage::getSingleton('adminhtml/session')->getStateData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStateData());
            Mage::getSingleton('adminhtml/session')->setStateData(null);
        } elseif (Mage::registry('state_data')) {
            $form->setValues(Mage::registry('state_data')->getData());
        }
        if($id){
            $resource = Mage::getSingleton('core/resource');
            $read = $resource->getConnection('core_read');
            $regionName = $resource->getTableName('directory/country_region_name');

            $select = $read->select()->from(array('region'=>$regionName))->where('region.region_id=?', $id);
            $data =$read->fetchAll($select);
            foreach($data as $row)
            {
                $form->addValues(array('name_'.$row['locale']=> $row['name']));
            }
        }
        return parent::_prepareForm();

    }
}
