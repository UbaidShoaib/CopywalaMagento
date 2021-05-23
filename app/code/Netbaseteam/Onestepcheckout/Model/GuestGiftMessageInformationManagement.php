<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\GiftMessageInformationManagementInterface;
use Netbaseteam\Onestepcheckout\Api\GuestGiftMessageInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestGiftMessageInformationManagement implements GuestGiftMessageInformationManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;
    /**
     * @var GiftMessageInformationManagementInterface
     */
    protected $giftMessageInformationManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        GiftMessageInformationManagementInterface $giftMessageInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->giftMessageInformationManagement = $giftMessageInformationManagement;
    }

    public function update($cartId, $giftMessage)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftMessageInformationManagement->update(
            $quoteIdMask->getQuoteId(),
            $giftMessage
        );
    }
}
