<?php

namespace Netbaseteam\Calculatorshipping\Model;

/**
 * Calculatorshipping Model
 *
 * @method \Netbaseteam\Calculatorshipping\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Calculatorshipping\Model\Resource\Page getResource()
 */
class Calculatorshipping extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Calculatorshipping\Model\ResourceModel\Calculatorshipping');
    }

}
