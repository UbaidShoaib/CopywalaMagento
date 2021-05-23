<?php

namespace Netbaseteam\PricingOption\Plugin\Order\Item;

class Renderer
{
    public function beforeToHtml(\Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject)
    {
        $subject->setTemplate('Netbaseteam_PricingOption::order/items/renderer/default.phtml');
    }
}