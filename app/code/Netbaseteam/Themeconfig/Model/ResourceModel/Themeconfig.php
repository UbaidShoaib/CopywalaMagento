<?php

namespace Netbaseteam\Themeconfig\Model\ResourceModel;

/**
 * Themeconfig Resource Model
 */
class Themeconfig extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('netbaseteam_themeconfig', 'themeconfig_id');
    }
}
