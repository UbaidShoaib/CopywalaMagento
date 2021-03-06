<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\GuestNewsletterManagementInterface;

class GuestNewsletterManagement implements GuestNewsletterManagementInterface
{
    protected $checkoutDataHelper;

    public function __construct(
        \Netbaseteam\Onestepcheckout\Helper\CheckoutData $checkoutDataHelper
    ) {
        $this->checkoutDataHelper = $checkoutDataHelper;
    }

    /**
     * Set payment information before redirect to payment for guest.
     *
     * @param string $cartId
     * @param string $email
     * @param mixed|null $amcheckoutData
     * @return void.
     */
    public function subscribe($cartId, $email, $amcheckoutData)
    {
        $this->checkoutDataHelper->beforePlaceOrder($amcheckoutData);

        $amcheckoutData = $this->_addGuestEmailForNewsletter($amcheckoutData, $email);

        $this->checkoutDataHelper->afterPlaceOrder($amcheckoutData);
    }

    protected function _addGuestEmailForNewsletter($data = [], $email)
    {
        if (isset($data['subscribe']) && $data['subscribe']) {
            $data['email'] = $email;
        }

        return $data;
    }
}