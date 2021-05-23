<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin;

use Magento\Store\Model\ScopeInterface;

class LayoutProcessor
{
    /**
     * @var array
     */
    protected $orderFixes = [];

    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\Onepage
     */
    private $onepageHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    function __construct(
        \Netbaseteam\Onestepcheckout\Helper\Onepage\Proxy $onepageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->onepageHelper = $onepageHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $field
     * @param $order
     */
    public function setOrder($field, $order)
    {
        $this->orderFixes[$field] = $order;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param $result
     * @return mixed
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        if ($this->scopeConfig->isSetFlag('netbaseteam_onestepcheckout/general/enabled', ScopeInterface::SCOPE_STORE)) {
            $layoutRoot = &$result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children'];

            foreach ($this->orderFixes as $code => $order) {
                $layoutRoot[$code]['sortOrder'] = $order;
            }

            foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                     ['payment']['children']['payments-list']['children'] as &$paymentMethod) {
                $paymentMethod['template'] = 'Netbaseteam_Onestepcheckout/billing-address';
            }

            $renderPaymentMethods = &$result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                                    ['payment']['children']['renders']['children'];

            if (isset($renderPaymentMethods['braintree'])) {
                $renderPaymentMethods['braintree']['component'] = 'Netbaseteam_Onestepcheckout/js/view/checkout/payment/braintree';
            }
        }

        return $result;
    }
}
