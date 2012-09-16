<?php

$installer = $this;
$installer->startSetup();
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$productTypes = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);


$array = array(
    array('code' => 'short_name',
        'type' => 'varchar',
        'Lable' => 'Short Name',
        'input' => 'text'),

    array('code' => 'voucher_title',
        'type' => 'varchar',
        'Lable' => 'Voucher Title',
        'input' => 'text'),

    array('code' => 'original_price',
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'input' => 'text',
        'Lable' => 'Original Price'),

    array('code' => 'deal_email',
        'type' => 'varchar',
        'Lable' => 'Email',
        'input' => 'text'),

    array('code' => 'company',
        'type' => 'text',
        'Lable' => 'Company Block',
        'input' => 'text'),

    array('code' => 'fine_print',
        'type' => 'text',
        'Lable' => 'Fine Print',
        'input' => 'textarea'),

    array('code' => 'highlights',
        'type' => 'text',
        'Lable' => 'Highlights',
        'input' => 'textarea'),

    array('code' => 'start_date',
        'type' => 'datetime',
        'input' => 'date',
        'backend' => 'eav/entity_attribute_backend_datetime',
        'Lable' => 'Start Date'),

    array('code' => 'end_date',
        'type' => 'datetime',
        'input' => 'date',
        'backend' => 'eav/entity_attribute_backend_datetime',
        'Lable' => 'End Date'),

    array('code' => 'voucher_date',
        'type' => 'datetime',
        'input' => 'date',
        'backend' => 'eav/entity_attribute_backend_datetime',
        'Lable' => 'Voucher Valid Date'),

    array('code' => 'sort',
        'type' => 'int',
        'type' => 'text',
        'default' => '0',
        'Lable' => 'Sort Order'),

    array('code' => 'min_qty',
        'type' => 'int',
        'type' => 'text',
        'Lable' => 'Minimum Orders to active the Deal'),

    array('code' => 'max_qty',
        'type' => 'int',
        'type' => 'text',
        'Lable' => 'Maximum Vouchers by customer'),

    array('code' => 'auto_renew',
        'type' => 'int',
        'input' => 'boolean',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => '0',
        'Lable' => 'Auto Renew Deal'),

    array('code' => 'main_deal',
        'type' => 'int',
        'input' => 'boolean',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => '0',
        'Lable' => 'Main Deal'),

    array('code' => 'side_deal',
        'type' => 'int',
        'input' => 'boolean',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => '0',
        'Lable' => 'Side Deal'),

    array('code' => 'recent_deal',
        'type' => 'int',
        'input' => 'boolean',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => '0',
        'Lable' => 'Show in Recent Deals'),

);

foreach ($array as $v) {
    $installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, $v['code']);
    //continue;

    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $v['code'], array(
        'type' => $v['type'],
        'group' => 'Deal',
        'backend' => !empty($v['backend']) ? $v['backend'] : '',
        'frontend' => !empty($v['frontend']) ? $v['frontend'] : '',
        'label' => $v['Lable'],
        'input' => !empty($v['input']) ? $v['input'] : 'text',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => false,
        'required' => false,
        'user_defined' => true,
        'default' => !empty($v['default']) ? $v['default'] : '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => $productTypes,
        'used_in_product_listing' => false
    ));
    if (!empty($v['input']) && $v['input'] == 'textarea') {
        $wysiwyg = true;
        $data = array('is_wysiwyg_enabled' => true);
        $id = $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, $v['code'], 'attribute_id');
        $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $id, $data, $value = null, $sortOrder = null);
    }
    continue;

}


$installer->endSetup();