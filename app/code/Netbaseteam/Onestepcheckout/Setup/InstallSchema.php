<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'netbaseteam_onestepcheckout_field'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('netbaseteam_onestepcheckout_field'))
            ->addColumn(
                'id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'EAV Attribute ID'
            )
            ->addColumn(
                'label',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Label'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Sort Order'
            )
            ->addColumn(
                'required',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Required'
            )
            ->addColumn(
                'width',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Width'
            )
            ->addColumn(
                'enabled',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Enabled'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_field',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Netbaseteam Checkout Field Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'netbaseteam_onestepcheckout_field_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('netbaseteam_onestepcheckout_field_store'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'field_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Netbaseteam Checkout Field ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'unsigned' => true],
                'Sort Order'
            )
            ->addColumn(
                'label',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Label'
            )
            ->addIndex(
                $installer->getIdxName(
                    'netbaseteam_onestepcheckout_field_store',
                    ['field_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['field_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_field_store',
                    'field_id',
                    'netbaseteam_onestepcheckout_field',
                    'id'
                ),
                'field_id',
                $installer->getTable('netbaseteam_onestepcheckout_field'),
                'id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_field_store',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Netbaseteam Checkout Field Store Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'netbaseteam_onestepcheckout_additional_fee'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('netbaseteam_onestepcheckout_additional_fee'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true],
                'Order Id'
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true],
                'Quote Id'
            )
            ->addColumn(
                'amount',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Amount'
            )
            ->addColumn(
                'base_amount',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Base Amount'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_additional_fee',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_additional_fee',
                    'quote_id',
                    'quote',
                    'entity_id'
                ),
                'quote_id',
                $installer->getTable('quote'),
                'entity_id',
                Table::ACTION_SET_NULL
            )
            ->setComment('Netbaseteam Checkout Additional Fee Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'netbaseteam_onestepcheckout_delivery'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('netbaseteam_onestepcheckout_delivery'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true],
                'Order Id'
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true],
                'Quote Id'
            )
            ->addColumn(
                'date',
                Table::TYPE_DATE,
                null,
                ['nullable' => true],
                'Delivery Date'
            )
            ->addColumn(
                'time',
                Table::TYPE_SMALLINT,
                2,
                ['nullable' => true],
                'Delivery Time'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_delivery',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'netbaseteam_onestepcheckout_delivery',
                    'quote_id',
                    'quote',
                    'entity_id'
                ),
                'quote_id',
                $installer->getTable('quote'),
                'entity_id',
                Table::ACTION_SET_NULL
            )
            ->setComment('Netbaseteam Checkout Delivery Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
