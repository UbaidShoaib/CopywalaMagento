<?php

namespace Netbaseteam\Megamenu\Model;

/**
 * Megamenu Model
 *
 * @method \Netbaseteam\Megamenu\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Megamenu\Model\Resource\Page getResource()
 */
class Megamenu extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Megamenu\Model\ResourceModel\Megamenu');
    }

}
