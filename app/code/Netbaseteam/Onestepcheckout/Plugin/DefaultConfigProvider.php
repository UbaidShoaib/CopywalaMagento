<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;

class DefaultConfigProvider
{
    const NETBASETEAM_STOCKSTATUS_MODULE_NAMESPACE = 'Netbaseteam_Stockstatus';

    const BLOCK_NAMES = [
        'block_shipping_address' => 'Shipping Address',
        'block_shipping_method' => 'Shipping Method',
        'block_delivery' => 'Delivery',
        'block_payment_method' => 'Payment Method',
        'block_order_summary' => 'Order Summary',
    ];

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\Item
     */
    private $itemHelper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Netbaseteam\Onestepcheckout\Model\ModuleEnable
     */
    private $moduleEnable;

    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Config
     */
    private $config;

    public function __construct(
        CheckoutSession $checkoutSession,
        \Netbaseteam\Onestepcheckout\Helper\Item $itemHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Netbaseteam\Onestepcheckout\Model\ModuleEnable $moduleEnable,
        \Netbaseteam\Onestepcheckout\Model\Config $config
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->layout = $layout;
        $this->itemHelper = $itemHelper;
        $this->moduleEnable = $moduleEnable;
        $this->config = $config;
    }

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $config)
    {
        if (!in_array('netbaseteam_onestepcheckout', $this->layout->getUpdate()->getHandles()))
            return $config;

        $quote = $this->checkoutSession->getQuote();

        foreach ($config['quoteItemData'] as &$item) {
            $additionalConfig = $this->itemHelper->getItemOptionsConfig($quote, $item['item_id']);

            if ($this->moduleEnable->isStockStatusEnable()) {
                $item['amstockstatus'] = $this->itemHelper->getItemCustomStockStatus($quote, $item['item_id']);
            }

            if (!empty($additionalConfig)) {
                $item['amcheckout'] = $additionalConfig;
            }
        }

        $this->getBlockNames($config);

        if ($this->moduleEnable->isPostNlEnable()) {
            $config['quoteData']['posnt_nl_enable'] = true;
        }

        return $config;
    }

    /**
     * @param $config
     */
    private function getBlockNames(&$config)
    {
        foreach (self::BLOCK_NAMES as $blockCode => $defaultName) {
            $blockName = $this->config->getBlockNames($blockCode);
            $config['quoteData'][$blockCode] = $blockName ?: __($defaultName);
        }
    }
}
