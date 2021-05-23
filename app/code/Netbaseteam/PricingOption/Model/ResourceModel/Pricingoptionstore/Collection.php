<?php

/**
 * Pricingoptionstore Resource Collection
 */
namespace Netbaseteam\PricingOption\Model\ResourceModel\Pricingoptionstore;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * Resource initialization
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('Netbaseteam\PricingOption\Model\Pricingoptionstore', 'Netbaseteam\PricingOption\Model\ResourceModel\Pricingoptionstore');
	}
}