<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin;

class AddressData
{
    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\Address
     */
    protected $addressHelper;

    public function __construct(
        \Netbaseteam\Onestepcheckout\Helper\Address $addressHelper
    ) {
        $this->addressHelper = $addressHelper;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        foreach ([$addressInformation->getShippingAddress(), $addressInformation->getBillingAddress()] as $address) {
            $this->addressHelper->fillEmpty($address);
        }

        return [$cartId, $addressInformation];
    }
}
