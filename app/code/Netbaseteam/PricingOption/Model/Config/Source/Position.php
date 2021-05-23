<?php

namespace Netbaseteam\PricingOption\Model\Config\Source;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'top', 'label' => __('Top')],
            ['value' => 'right', 'label' => __('Right')],
            ['value' => 'bottom', 'label' => __('Bottom')],
            ['value' => 'left', 'label' => __('Left')]
        ];
    }
}