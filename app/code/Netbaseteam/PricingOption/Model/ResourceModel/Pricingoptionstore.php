<?php

namespace Netbaseteam\PricingOption\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Pricingoptionstore Resource Model
 */
class Pricingoptionstore extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('nb_pricingoption_store', 'id');
	}
}