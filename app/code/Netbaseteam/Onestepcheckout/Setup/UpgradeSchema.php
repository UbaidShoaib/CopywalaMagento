<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->addDeliveryDateCommentColumn($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addDeliveryDateCommentColumn(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('netbaseteam_onestepcheckout_delivery');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'comment',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Delivery Comment'
            ]
        );
    }
}
