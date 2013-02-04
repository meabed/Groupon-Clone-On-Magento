<?php
class Web_Voucher_Block_Adminhtml_Voucher_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('voucher_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $storeId = Mage::app()->getStore()->getId();
        $ordersTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $productTable = Mage::getResourceModel('catalog/product_flat')->getFlatTableName(1);
        //        $productTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_flat').'_1';
        $collection = Mage::getModel('voucher/vouchers')->getCollection();
        $collection->getSelect()->columns(new Zend_Db_Expr("CONCAT(orders.customer_firstname, ' ',orders.customer_lastname) AS billing_name"));
        $collection->getSelect()
                ->joinLeft(array('orders' => $ordersTable), 'main_table.order_id=orders.entity_id', array('orders.customer_firstname', 'orders.customer_lastname', 'order_status' => 'orders.status'));
        $collection->getSelect()
                ->joinLeft(array('product' => $productTable), 'main_table.product_id = product.entity_id', array('name'));
        $this->setCollection($collection);

        $this->addExportType('*/*/exportCsv', Mage::helper('voucher')->__('CSV'));

        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
                                           'header' => Mage::helper('voucher')->__('ID'),
                                           'align' => 'right',
                                           'width' => '50px',
                                           'index' => 'entity_id',
                                      ));

        $this->addColumn('deal_voucher_code', array(
                                                   'header' => Mage::helper('voucher')->__('Code'),
                                                   'align' => 'left',
                                                   'width' => '110px',
                                                   'index' => 'deal_voucher_code',
                                              ));
        $this->addColumn('order_increment_id', array(
                                                    'header' => Mage::helper('voucher')->__('Order ID'),
                                                    'align' => 'left',
                                                    'width' => '80px',
                                                    'index' => 'order_increment_id',
                                               ));
        $this->addColumn('product_name', array(
                                              'header' => Mage::helper('voucher')->__('Product Name'),
                                              'align' => 'left',
                                              'index' => 'name'
                                         ));
        $this->addColumn('billing_name', array(
                                              'header' => Mage::helper('voucher')->__('Bill to Name'),
                                              'align' => 'left',
                                              'index' => 'billing_name'
                                         ));
        $this->addColumn('created_at', array(
                                            'header' => Mage::helper('voucher')->__('Creation Time'),
                                            'align' => 'left',
                                            'width' => '120px',
                                            'type' => 'date',
                                            'default' => '--',
                                            'index' => 'created_at',
                                       ));

        $this->addColumn('updated_at', array(
                                            'header' => Mage::helper('voucher')->__('Update Time'),
                                            'align' => 'left',
                                            'width' => '120px',
                                            'type' => 'date',
                                            'default' => '--',
                                            'index' => 'updated_at',
                                       ));


        $this->addColumn('status', array(

                                        'header' => Mage::helper('voucher')->__('Status'),
                                        'align' => 'left',
                                        'width' => '80px',
                                        'index' => 'status',
                                        'type' => 'options',
                                        'options' => Mage::getModel('voucher/vouchers')->getStatuses()
                                   ));

        $this->addColumn('order_status', array(

                                              'header' => Mage::helper('voucher')->__('Order Status'),
                                              'align' => 'left',
                                              'width' => '80px',
                                              'index' => 'order_status',
                                              'filter_index' => 'orders.status'

                                         ));

        $this->addColumn('is_sent', array(
            'header' => Mage::helper('voucher')->__('Sent'),
            'align' => 'left',
            'width' => '20px',
            'index' => 'is_sent',

        ));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('voucher');
        //
        //        $this->getMassactionBlock()->addItem('delete', array(
        //                                                            'label' => Mage::helper('voucher')->__('Delete'),
        //                                                            'url' => $this->getUrl('*/*/massDelete'),
        //                                                            'confirm' => Mage::helper('voucher')->__('Are you sure?')
        //                                                       ));
        //array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
                                                            'label' => Mage::helper('voucher')->__('Change status'),
                                                            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
                                                            'additional' => array(
                                                                'visibility' => array(
                                                                    'name' => 'status',
                                                                    'type' => 'select',
                                                                    'class' => 'required-entry',
                                                                    'label' => Mage::helper('voucher')->__('Status'),
                                                                    'values' => Mage::getSingleton('voucher/vouchers')->getStatuses()
                                                                )
                                                            )
                                                       ));
        $this->getMassactionBlock()->addItem('is_sent', array(
                                                             'label' => Mage::helper('voucher')->__('Send'),
                                                             'url' => $this->getUrl('*/*/massSend', array('_current' => true)),
                                                        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}