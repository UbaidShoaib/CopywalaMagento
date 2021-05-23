<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Api;

use Netbaseteam\Onestepcheckout\Api\Data\FeeInterface;

interface FeeRepositoryInterface
{
    /**
     * @param FeeInterface $fee
     * @return FeeInterface
     */
    public function save(FeeInterface $fee);

    /**
     * @param int $feeId
     * @return FeeInterface
     */
    public function getById($feeId);

    /**
     * @param Data\FeeInterface $fee
     * @return bool true on success
     */
    public function delete(FeeInterface $fee);

    /**
     * @param int $feeId
     * @return bool true on success
     */
    public function deleteById($feeId);

    /**
     * @param int $quoteId
     * @return FeeInterface
     */
    public function getByQuoteId($quoteId);

    /**
     * @param int $orderId
     * @return FeeInterface
     */
    public function getByOrderId($orderId);
}
