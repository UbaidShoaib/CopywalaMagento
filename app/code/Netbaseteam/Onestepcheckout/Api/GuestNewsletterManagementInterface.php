<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Api;

interface GuestNewsletterManagementInterface
{
    /**
     * Set payment information before redirect to payment for guest.
     *
     * @param string $cartId
     * @param string $email
     * @param mixed|null $amcheckoutData
     * @return void.
     */
    public function subscribe($cartId, $email, $amcheckoutData);
}