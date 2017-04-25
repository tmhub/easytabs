<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Alter table 'easytabs/tab'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('easytabs/tab'),
        'conditions_serialized',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => null,
            'comment' => 'Conditions Serialized'
        )
    );

$installer->endSetup();
