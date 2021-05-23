<?php

/**
 * Themeconfig Resource Collection
 */
namespace Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Themeconfig\Model\Themeconfig', 'Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig');
    }
}
