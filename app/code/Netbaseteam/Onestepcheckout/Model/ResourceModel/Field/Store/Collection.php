<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('\Netbaseteam\Onestepcheckout\Model\Field\Store', '\Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store');
    }
}
