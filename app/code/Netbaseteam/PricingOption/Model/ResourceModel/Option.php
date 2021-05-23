<?php

namespace Netbaseteam\PricingOption\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * \Option Resource Model
 */
class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	/**
	 * Block store table
	 *
	 * @var string
	 */
	protected $_blockStoreTable;

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('netbaseteam_pricingoption_option', 'id');
		$this->_blockStoreTable = $this->getTable('nb_pricingoption_store');
	}

	/**
	 * Retrieve store IDs related to given rating
	 *
	 * @param  int $optionId
	 * @return array
	 */
	public function getStores($optionId) {
		$select = $this->getConnection()->select()->from(
			$this->getTable($this->_blockStoreTable),
			'store_id'
		)->where(
			'id = ?',
			$optionId
		);
		return $this->getConnection()->fetchCol($select);
	}

	/**
	 * Perform actions before object save
	 *
	 * @param AbstractModel $object
	 * @return $this
	 */
	protected function _beforeSave(AbstractModel $object) {

		if ($object->hasData('stores') && is_array($object->getStores())) {
			$stores = $object->getStores();
			$stores[] = 0;
			$object->setStores($stores);
		} elseif ($object->hasData('stores')) {
			$object->setStores([$object->getStores(), 0]);
		}

		return $this;
	}

	/**
	 * Perform actions after object save
	 *
	 * @param \Magento\Framework\Model\AbstractModel $object
	 * @return $this
	 */
	protected function _afterSave(AbstractModel $object) {
		$connection = $this->getConnection();

		/**
		 * save stores
		 */
		$stores = $object->getStores();
		$optionId = $object->getId();

		unset($stores[1]);
		if (!empty($stores)) {

			$storeId = $stores[0] ? $stores[0] : 0;
			$read = "SELECT id FROM {$this->_blockStoreTable} WHERE option_id = {$optionId} AND store_id = {$storeId}";
			$result = $connection->fetchAll($read);
			if (!empty($result)) {

				$fields = $object['fields'] ? $object['fields'] : 1;
				$sql = "Update " . $this->_blockStoreTable . " Set fields = '" . $fields . "'  Where option_id = " . $optionId . " && store_id = " .$storeId;
				$connection->query($sql);

			} else {

				$storeId = $stores[0] ? $stores[0] : 0;
				$fields = $object['fields'] ? $object['fields'] : '';
				$storeInsert = ['option_id' => $optionId, 'fields' => $object['fields'], 'store_id' => $storeId];
				$connection->insert($this->_blockStoreTable, $storeInsert);
			}
		}

		return $this;
	}
}