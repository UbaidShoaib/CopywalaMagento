<?php

namespace Netbaseteam\PricingOption\Plugin\Cart\Item;

class Renderer
{
    public function beforeToHtml(\Magento\Checkout\Block\Cart\Item\Renderer $subject)
    {
        $subject->setTemplate('Netbaseteam_PricingOption::cart/items/renderer/default.phtml');
    }
}