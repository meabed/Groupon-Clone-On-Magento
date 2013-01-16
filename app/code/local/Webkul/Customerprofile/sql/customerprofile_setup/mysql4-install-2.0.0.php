<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Add Nickname Attribute to Customer
 */
$installer->addAttribute('customer', 'nickname', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Screen Name',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => true,
        'unique'            => false,
    ));

/**
 * Add ShortProfile Attribute to Customer
 */
$installer->addAttribute('customer', 'shortprofile', array(
        'type'              => 'text',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Short profile',
        'input'             => 'textarea',
        'class'             => '',
        'source'            => '',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => true,
        'unique'            => false,
    ));

/**
 * Add Gender Attribute to Customer
 */
$installer->addAttribute('customer', 'gender', array(
        'type'              => 'text',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Gender',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'customerprofile/gendertype',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => null,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => true,
        'unique'            => false,
    ));
/*$installer->run("

INSERT INTO eav_attribute SET entity_type_id=1, attribute_code='nickname', backend_model='', backend_type='varchar',
backend_table='', frontend_model='', frontend_input='text', frontend_input_renderer='', frontend_label='Screen Name',
source_model='', default_value='', apply_to='', position=0, note='';

INSERT INTO eav_attribute SET entity_type_id=1, attribute_code='shortprofile', backend_model='', backend_type='text',
backend_table='', frontend_model='', frontend_input='textarea', frontend_input_renderer='', frontend_label='Short profile',
source_model='', default_value='', apply_to='', position=0, note='';

INSERT INTO eav_attribute SET entity_type_id=1, attribute_code='gender', backend_model='', backend_type='text',
backend_table='', frontend_model='', frontend_input='select', frontend_input_renderer='', frontend_label='Gender',
source_model='customerprofile/gendertype', default_value=null, apply_to='', position=0, note='';


");*/

#$installer->installEntities();

$installer->endSetup();