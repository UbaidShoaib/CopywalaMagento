<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Api;

use Magento\Quote\Api\Data\AddressInterface;

interface ItemManagementInterface
{
    /**
     * @param int              $cartId
     * @param int              $itemId
     * @param AddressInterface $address
     *
     * @return \Netbaseteam\Onestepcheckout\Api\Data\TotalsInterface|boolean
     */
    public function remove($cartId, $itemId, AddressInterface $address);

    /**
     * @param int              $cartId
     * @param int              $itemId
     * @param string           $formData
     * @param AddressInterface $address
     *
     * @return \Netbaseteam\Onestepcheckout\Api\Data\TotalsInterface|boolean
     */
    public function update($cartId, $itemId, $formData, AddressInterface $address);
}
