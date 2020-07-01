<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'easytabs/tab'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('easytabs/tab'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
        ), 'Tab Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Title')
    ->addColumn('alias', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        'nullable'  => true
        ), 'Alias')
    ->addColumn('block', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Block')
    ->addColumn('custom_option', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Custom Option')
    ->addColumn('template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Template')
    ->addColumn('unset', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Unset')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Sort Order')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array()
        , 'Status')
    ->addColumn('product_tab', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array()
        , 'Product Tab')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array()
        , 'Content')
    ->setComment('Easy Tabs');
$installer->getConnection()->createTable($table);

/**
 * Create table 'easytabs/store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('easytabs/store'))
    ->addColumn('tab_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned' => true,
        'primary'   => true,
        ), 'Tab ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addIndex($installer->getIdxName('easytabs/store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('easytabs/store', 'tab_id', 'easytabs/tab', 'id'),
        'tab_id', $installer->getTable('easytabs/tab'), 'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('easytabs/store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Tabs To Stores Linkage Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
