<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Api;

interface DeliveryInformationManagementInterface
{
    /**
     * @param int $cartId
     * @param string $date
     * @param int    $time
     * @param string $comment
     *
     * @return bool
     */
    public function update($cartId, $date, $time, $comment);
}
