<?php

namespace Netbaseteam\PricingOption\Model\Config\Source;

class DisplayProductOption implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Popup')],
            ['value' => '2', 'label' => __('Product Tab')]
        ];
    }
}