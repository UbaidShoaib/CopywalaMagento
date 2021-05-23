<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin;

class CheckoutHelperPlugin
{
    /**
     * Return true to equality duplicating billing and shipping address in placed order. If they were different.
     *
     * @param \Magento\Checkout\Helper\Data $subject
     * @param $result
     * @return bool
     */
    public function afterIsDisplayBillingOnPaymentMethodAvailable(\Magento\Checkout\Helper\Data $subject, $result)
    {
        return $result;
    }
}
