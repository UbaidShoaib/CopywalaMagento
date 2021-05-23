<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Onepage\Success;

use Magento\Store\Model\ScopeInterface;

class Cms extends \Magento\Cms\Block\Block
{
    public function getBlockId()
    {
        return (int)$this->_scopeConfig->getValue(
            'netbaseteam_onestepcheckout/success_page/block_id', ScopeInterface::SCOPE_STORE
        );
    }
}
