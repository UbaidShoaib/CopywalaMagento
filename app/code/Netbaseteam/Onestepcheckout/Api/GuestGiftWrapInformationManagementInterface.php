<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Api;

interface GuestGiftWrapInformationManagementInterface
{
    /**
     * Calculate quote totals based on quote and fee
     *
     * @param string $cartId
     * @param bool $checked
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function update($cartId, $checked);
}
