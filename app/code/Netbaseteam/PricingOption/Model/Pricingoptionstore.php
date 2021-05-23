<?php

namespace Netbaseteam\PricingOption\Model;

class Pricingoptionstore extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\PricingOption\Model\ResourceModel\Pricingoptionstore');
    }
}