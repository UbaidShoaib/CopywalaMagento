<?php

/**
 * Netbaseteam.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the cmsmart.net license that is
 * available through the world-wide-web at this URL:
 * *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Netbaseteam
 * @package     Netbaseteam_Megamenu
 * @copyright   Copyright (c) Netbaseteam (http://www.cmsmart.net/)
 *
 */

namespace Netbaseteam\Megamenu\Model\Config\Source;

class Menumobiletype implements \Magento\Framework\Option\ArrayInterface
{
    const HORIZONTAL = 1;
    const VERTICAL = 2; 

    public function getOptionArray()
    {
        return array(
            self::HORIZONTAL => __('Horizontal'),
            self::VERTICAL => __('Vertical') 
        );
    }

    public function toOptionArray()
    {
        return array(
            self::HORIZONTAL => __('Horizontal'),
            self::VERTICAL => __('Vertical') 
        );
    }
}