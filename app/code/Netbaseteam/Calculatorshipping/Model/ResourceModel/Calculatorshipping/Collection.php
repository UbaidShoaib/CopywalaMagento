<?php

/**
 * Calculatorshipping Resource Collection
 */
namespace Netbaseteam\Calculatorshipping\Model\ResourceModel\Calculatorshipping;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Calculatorshipping\Model\Calculatorshipping', 'Netbaseteam\Calculatorshipping\Model\ResourceModel\Calculatorshipping');
    }
}
