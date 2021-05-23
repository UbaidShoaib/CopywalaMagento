<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model\ResourceModel;

class Delivery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('netbaseteam_onestepcheckout_delivery', 'id');
    }
}
