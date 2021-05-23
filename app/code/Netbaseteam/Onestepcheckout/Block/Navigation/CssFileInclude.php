<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Navigation;

class CssFileInclude extends \Magento\Framework\View\Element\Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->addPageAsset('Netbaseteam_Onestepcheckout::css/source/mkcss/am-checkout.css');
    }
}
