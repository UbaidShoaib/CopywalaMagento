<?php
namespace Netbaseteam\Themeconfig\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableName = $setup->getTable('sales_order_status');
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order_status'),
                    'color',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'unsigned' => true,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Color Column'
                    ]
                );
            }
        }
        $setup->endSetup();
    }
}
?>