<?php

namespace Netbaseteam\PricingOption\Model\Config\Source;

class DisplayStyle implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Style 1')],
            ['value' => '2', 'label' => __('Style 2')]
        ];
    }
}