<?php

/**
 * Netbaseteam.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the cmsmart.net license that is
 * available through the world-wide-web at this URL:
 * *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Netbaseteam
 * @package     Netbaseteam_Megamenu
 * @copyright   Copyright (c) Netbaseteam (http://www.cmsmart.net/)
 *
 */

namespace Netbaseteam\Megamenu\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            // Get module table
            $tableName = $setup->getTable('cmsmart_megamenu');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {

                $setup->getConnection()->addColumn($tableName, 'icon', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Menu Item Icon'
                ]);
                $setup->getConnection()->addColumn($tableName, 'menu_name', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Name'
                ]);
                $setup->getConnection()->addColumn($tableName, 'link', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Link'
                ]);
                $setup->getConnection()->addColumn($tableName, 'store_ids', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Views'
                ]);
                $setup->getConnection()->addColumn($tableName, 'iconselect_class', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Icon Select Class'
                ]);

            }
        }
        $setup->endSetup();
    }
}
