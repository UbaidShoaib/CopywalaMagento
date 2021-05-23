<?php

/**
 * Megamenu Resource Collection
 */
namespace Netbaseteam\Megamenu\Model\ResourceModel\Megamenu;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Megamenu\Model\Megamenu', 'Netbaseteam\Megamenu\Model\ResourceModel\Megamenu');
    }
}
