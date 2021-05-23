<?php

namespace Netbaseteam\Calculatorshipping\Model\ResourceModel;

/**
 * Calculatorshipping Resource Model
 */
class Calculatorshipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('netbaseteam_calculatorshipping', 'calculatorshipping_id');
    }
}
