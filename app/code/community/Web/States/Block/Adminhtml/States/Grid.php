<?php
class Web_States_Block_Adminhtml_States_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('web_states_grid');
        $this->setDefaultSort('region_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('web_states/states')->getCollection();
        $this->setCollection($collection);

        $this->setLocales(Mage::helper('web_states')->getLocales());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'region_id', array(
                              'header'           => Mage::helper('web_states')->__('ID'),
                              'align'            => 'left',
                              'width'            => '5',
                              'index'            => 'region_id',
                              'column_css_class' => 'row_id'
                         )
        );

        $this->addColumn(
            'country_id', array(
                               'header' => Mage::helper('web_states')->__('Country Code'),
                               'align'  => 'left',
                               'width'  => '110px',
                               'index'  => 'country_id',
                               'type'   => 'country',
                          )
        );
        $this->addColumn(
            'code', array(
                         'header'           => Mage::helper('web_states')->__('Region Code'),
                         'align'            => 'left',
                         'width'            => '110px',
                         'index'            => 'code',
                         //'editable' =>true,
                         'column_css_class' => 'code_td'
                    )
        );
        $this->addColumn(
            'default_name', array(
                                 'header'           => Mage::helper('web_states')->__('Default Name'),
                                 'align'            => 'left',
                                 'width'            => '110px',
                                 'index'            => 'default_name',
                                 //'editable' =>true,
                                 'column_css_class' => 'default_name'
                            )
        );
        $this->addColumn(
            'name_locale', array(
                                 'header'           => Mage::helper('web_states')->__('Name in Locale'),
                                 'align'            => 'left',
                                 'width'            => '110px',
                                 'index'            => 'region_id',
                                 'sortable'         => false,
                                 'filter'           => false,
                                 'renderer'  =>     'Web_States_Block_Adminhtml_Renderer_Name',

                                 //'editable' =>true,
                                 'column_css_class' => 'name_locale'
                            )
        );
        $this->addColumn(
            'action',
            array(
                 'header'    => Mage::helper('web_states')->__('Action'),
                 'width'     => '50px',
                 'type'      => 'action',
                 'getter'    => 'getRegionId',
                 'actions'   => array(
                     array(
                         'caption' => Mage::helper('web_states')->__('View'),
                         'url'     => array('base' => '*/*/edit'),
                         'field'   => 'region_id'
                     )
                 ),
                 'filter'    => false,
                 'sortable'  => false,
                 'index'     => 'region_id',
                 'is_system' => true,
            )
        );
        $this->setAdditionalJavaScript($this->getScripts());
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '';
        return $this->getUrl('*/*/edit', array('id' => $row->getRegionId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('region_id');
        $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->setFormFieldName('web_states');
        $this->getMassactionBlock()->addItem(
            'delete', array(
                           'label' => Mage::helper('web_states')->__('Delete'),
                           'url'   => $this->getUrl('*/*/massDelete', array('_current' => true)),
                      )
        );
        return $this;
    }

    public function getScripts()
    {
        $locales = Mage::helper('web_states')->getLocales();

        $nameUrl = $this->getUrl('*/*/saveName');
        $codeUrl = $this->getUrl('*/*/saveCode');
        $js
            = '
        function getNameUrl(e)
        {
            return "' . $nameUrl . 'region_id/"+getId(e);
        }
        function getCodeUrl(e)
        {
            return "' . $codeUrl . 'region_id/"+getId(e);
        }
        function getNameLocaleUrl(e,url)
        {
            return url+"region_id/"+getId(e);
        }
        function getId(e)
        {
            id = e.up("tr").down("td.row_id").innerHTML;
            return id.trim();
        }
        ';
        $js
            .= <<<EOF
        document.observe('dom:loaded', function() {
            $$('.default_name').each(function(el){
                if(el.down('span')){return ;}
                idx = getId(el);
                el.update('<span id='+idx+'>'+el.innerHTML.trim()+'</span>');
                new Ajax.InPlaceEditor(el.down('span'), getNameUrl(el),{formId:idx,okText: 'Save',cancelText: 'Cancel'} );
            });
            $$('.code_td').each(function(el){
                if(el.down('span')){return ;}
                idx = getId(el);
                el.update('<span id='+idx+'>'+el.innerHTML.trim()+'</span>');
                new Ajax.InPlaceEditor(el.down('span'), getCodeUrl(el),{formId:idx,okText: 'Save',cancelText: 'Cancel'} );
            });

EOF;
        foreach($locales as $locale)
        {
            $nameLocaleUrl = $this->getUrl('*/*/saveNameLocale',array('locale'=>$locale));
            $e_name = $locale.'_name';
            $js
                .= <<<EOF
                $$('.$e_name').each(function(el){
                idx = getId(el);
                el.update('<span id='+idx+'>'+el.innerHTML.trim()+'</span>');
                new Ajax.InPlaceEditor(el.down('span'), getNameLocaleUrl(el,'$nameLocaleUrl'),{formId:idx,okText: 'Save',cancelText: 'Cancel'} );
            });

EOF;

        }
        $js .='});';
        return $js;

    }

}