<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\DeliveryInformationManagementInterface;
use Netbaseteam\Onestepcheckout\Api\GuestDeliveryInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestDeliveryInformationManagement implements GuestDeliveryInformationManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;
    /**
     * @var DeliveryInformationManagementInterface
     */
    protected $deliveryInformationManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        DeliveryInformationManagementInterface $deliveryInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;

        $this->deliveryInformationManagement = $deliveryInformationManagement;
    }

    /**
     * @param string $cartId
     * @param string $date
     * @param int $time
     * @param string $comment
     * @return bool
     */
    public function update($cartId, $date, $time, $comment)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->deliveryInformationManagement->update(
            $quoteIdMask->getQuoteId(),
            $date,
            $time,
            $comment
        );
    }
}
