<?php
class Web_States_Adminhtml_StatesController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/web_states');
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $regionId = $this->getRequest()->getParam('region_id');
        $state = Mage::getModel('web_states/states')->load($regionId);

        if ($state->getRegionId() || $regionId == 0) {
            $this->_initAction();
            Mage::register('state_data', $state);
            $this->_addBreadcrumb(Mage::helper('web_states')->__('Country/States Manager'), Mage::helper('web_states')->__('Item Manager'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('web_states/adminhtml_states_edit'))
                ->_addLeft($this->getLayout()->createBlock('web_states/adminhtml_states_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web_states')->__('State does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        if ($this->getRequest()->getPost()) {
            $id = $request->getParam('id');
            $code = $request->getParam('code');
            $name = $request->getParam('default_name');
            $countryId = $request->getParam('country_id');
            if (!$name || !$code) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please fill the required fields'));
                $this->_redirect('*/*/');
                return;
            }
            $state = Mage::getModel('web_states/states')->getCollection()
                ->addFieldToFilter('code', $code)
                ->addFieldToFilter('country_id', $countryId)
                ->getAllIds();
            if (count($state) > 0 && !in_array($id, $state)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('State/Country combination must be unique'));
                $this->_redirect('*/*/edit', array('region_id' => $id));
                return;
            }

            try {
                $state = Mage::getModel('web_states/states');
                $state->setRegionId($id)
                    ->setCode($code)
                    ->setCountryId($countryId)
                    ->setDefaultName($name)
                    ->save();
                if ($state->getRegionId()) {
                    $locales = Mage::helper('web_states')->getLocales();
                    $resource = Mage::getSingleton('core/resource');
                    $write = $resource->getConnection('core_write');
                    $regionName = $resource->getTableName('directory/country_region_name');
                    $write->delete($regionName, array('region_id =' . $state->getRegionId()));
                    foreach ($locales as $locale) {
                        $localeName = $request->getParam('name_' . $locale);
                        if ($localeName) {
                            $write->insert($regionName, array('region_id' => $state->getRegionId(), 'locale' => $locale, 'name' => trim($name)));
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->getStateData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setStateData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('region_id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function saveNameAction()
    {
        $request = $this->getRequest();
        $editorId = $request->getParam('editorId');
        $value = $request->getParam('value');
        if (!$editorId) {
            echo $this->__('Unable to Save.');
            return;
        }
        if (!$value) {
            echo $this->__('Value can not be empty.');
            return;
        }
        $model = Mage::getModel('web_states/states')->load($editorId);
        $model->setDefaultName(trim($value));
        try {
            $model->save();
        } catch (Exception $e) {
            echo $e->getCode() . '-' . $e->getMessage();
        }
        echo $model->getDefaultName();

    }

    public function saveNameLocaleAction()
    {
        $request = $this->getRequest();
        $editorId = $request->getParam('editorId');
        $locale = $request->getParam('locale');
        $value = $request->getParam('value');
        if (!$editorId) {
            echo $this->__('Unable to Save.');
            return;
        }
        if (!$locale) {
            echo $this->__('Locale can not be empty.');
            return;
        }
        if ($value == 'EMPTY' || $value == 'Empty value will not be saved.') {
            echo $this->__('Empty value will not be saved.');
            return;
        }
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write');
        $regionName = $resource->getTableName('directory/country_region_name');
        $write->delete($regionName, array('region_id =' . $editorId,'locale = "' . $locale.'"'));

        if ($value) {
            $write->insert($regionName, array('region_id' => $editorId, 'locale' => $locale, 'name' => trim($value)));
        }
        $select = $write->select('*')->from(array('region' => $regionName))->where('region.region_id=?', $editorId)->where('region.locale=?', $locale);
        $row = $write->fetchRow($select);
        echo $row['name'];
    }

    public function saveCodeAction()
    {
        $request = $this->getRequest();
        $editorId = $request->getParam('editorId');
        $value = $request->getParam('value');
        if (!$editorId) {
            echo $this->__('Unable to Save.');
            return;
        }
        if (!$value) {
            echo $this->__('Value can not be empty.');
            return;
        }
        $row = Mage::getModel('web_states/states')->getCollection()
            ->addFieldToFilter('code', $value)
            ->getFirstItem();
        if (($row->getRegionId() == $editorId) && (trim($value) == $row->getCode())) {
            echo $row->getCode() . ' not updated';
            return;
        }
        if ($row->getRegionId()) {
            echo $this->__('State code must be unique.');
            return;
        }

        $model = Mage::getModel('web_states/states')->load($editorId);
        $model->setCode(trim($value));
        try {
            $model->save();
        } catch (Exception $e) {
            echo $e->getCode() . '-' . $e->getMessage();
        }
        echo $model->getCode();
    }

    public function massDeleteAction()
    {
        $stateIds = $this->getRequest()->getParam('web_states');
        if (!is_array($stateIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select state(s).'));
        } else {
            try {
                $state = Mage::getModel('web_states/states');
                foreach ($stateIds as $stateId) {
                    $state->load($stateId)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($stateIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');

    }

}