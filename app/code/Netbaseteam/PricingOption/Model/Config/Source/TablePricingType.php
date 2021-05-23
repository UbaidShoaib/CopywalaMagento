<?php

namespace Netbaseteam\PricingOption\Model\Config\Source;

class TablePricingType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Quantity range')],
            ['value' => '2', 'label' => __('Quantity breaks')]
        ];
    }
}