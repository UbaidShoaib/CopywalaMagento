<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model\Field;

use Magento\Framework\Model\AbstractModel;

class Store extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store');
    }
}
