<?php

namespace Netbaseteam\PricingOption\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup,ModuleContextInterface $context) 
	{
		$setup->startSetup();
		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			/**
			 * Create table 'nb_pricingoption_store'
			 */
			$table = $setup->getConnection()
				->newTable($setup->getTable('nb_pricingoption_store'))
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'Entity Id'
				)->addColumn(
					'option_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'nullable' => false],
					'Option Id'
				)->addColumn(
	                'fields',
	                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                null,
	                ['unsigned' => true, 'nullable' => null],
	                'Fields'
	            )->addColumn(
					'store_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
					null,
					['unsigned' => true, 'nullable' => false],
					'Store Id'
				);

			$setup->getConnection()->createTable($table);
		}

		$setup->endSetup();
	}
}