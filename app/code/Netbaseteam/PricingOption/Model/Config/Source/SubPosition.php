<?php

namespace Netbaseteam\PricingOption\Model\Config\Source;

class SubPosition implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'b', 'label' => __('Bellow')],
            ['value' => 'r', 'label' => __('Right')]
        ];
    }
}