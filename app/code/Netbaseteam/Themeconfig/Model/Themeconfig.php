<?php

namespace Netbaseteam\Themeconfig\Model;

/**
 * Themeconfig Model
 *
 * @method \Netbaseteam\Themeconfig\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Themeconfig\Model\Resource\Page getResource()
 */
class Themeconfig extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig');
    }

}
