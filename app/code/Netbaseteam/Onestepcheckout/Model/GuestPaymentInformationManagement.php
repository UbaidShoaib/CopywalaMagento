<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class GuestPaymentInformationManagement extends \Magento\Checkout\Model\GuestPaymentInformationManagement
    implements GuestPaymentInformationManagementInterface
{
    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\CheckoutData
     */
    protected $checkoutDataHelper;

    public function __construct(
        \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Api\GuestPaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\GuestCartManagementInterface $cartManagement,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        \Netbaseteam\Onestepcheckout\Helper\CheckoutData $checkoutDataHelper
    ) {
        parent::__construct(
            $billingAddressManagement, $paymentMethodManagement, $cartManagement, $paymentInformationManagement,
            $quoteIdMaskFactory, $cartRepository
        );
        $this->checkoutDataHelper = $checkoutDataHelper;
    }

    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null,
        $amcheckout = null
    ) {
        $this->checkoutDataHelper->beforePlaceOrder($amcheckout);

        $result = parent::savePaymentInformationAndPlaceOrder($cartId, $email, $paymentMethod, $billingAddress);

        $amcheckout = $this->_addGuestEmailForNewsletter($amcheckout, $email);

        $this->checkoutDataHelper->afterPlaceOrder($amcheckout);

        return $result;
    }

    protected function _addGuestEmailForNewsletter($data = [], $email)
    {
        if (isset($data['subscribe']) && $data['subscribe']) {
            $data['email'] = $email;
        }

        return $data;
    }
}
