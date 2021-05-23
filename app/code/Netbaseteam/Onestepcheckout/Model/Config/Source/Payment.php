<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model\Config\Source;

class Payment extends \Magento\Payment\Model\Config\Source\Allmethods
{
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $options = parent::toOptionArray();

        array_unshift($options, ['value' => '', 'label' => ' ']);

        return $options;
    }
}
