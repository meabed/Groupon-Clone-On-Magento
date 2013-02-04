<?php
/**
 *
 * @category   MW
 * @package    MW_Credit
 * @author     Mage-World.com <support@mage-world.com>
 */
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute('catalog_product', 'credit_enabled', array(
        'group'             => 'General',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Credit Enabled',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'virtual', // not run in 1.4
        'is_configurable'   => false
    ));
    
// make these attributes applicable to virtual products
$field = 'credit_enabled';
$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    
 if (!in_array('virtual', $applyTo)) {
 	$applyTo[] = 'virtual';
    $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
}

$installer->endSetup();