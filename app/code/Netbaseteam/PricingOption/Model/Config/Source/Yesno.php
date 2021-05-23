<?php

namespace Netbaseteam\PricingOption\Model\Config\Source;

class Yesno implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'yes', 'label' => __('Yes')],
            ['value' => 'no', 'label' => __('No')]
        ];
    }
}