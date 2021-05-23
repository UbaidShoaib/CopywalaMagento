<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\NewsletterManagementInterface;

class NewsletterManagement implements NewsletterManagementInterface
{
    protected $checkoutDataHelper;

    public function __construct(
        \Netbaseteam\Onestepcheckout\Helper\CheckoutData $checkoutDataHelper
    ) {
        $this->checkoutDataHelper = $checkoutDataHelper;
    }

    /**
     * Set payment information before redirect to payment for customer.
     *
     * @param string $cartId
     * @param mixed|null $amcheckoutData
     * @return void.
     */
    public function subscribe($cartId, $amcheckoutData)
    {
        $this->checkoutDataHelper->beforePlaceOrder($amcheckoutData);

        $this->checkoutDataHelper->afterPlaceOrder($amcheckoutData);
    }
}