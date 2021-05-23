<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\GiftWrapInformationManagementInterface;
use Netbaseteam\Onestepcheckout\Api\GuestGiftWrapInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestGiftWrapInformationManagement implements GuestGiftWrapInformationManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;
    /**
     * @var GiftWrapInformationManagementInterface
     */
    protected $giftWrapInformationManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        GiftWrapInformationManagementInterface $giftWrapInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->giftWrapInformationManagement = $giftWrapInformationManagement;
    }

    public function update($cartId, $checked)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftWrapInformationManagement->update(
            $quoteIdMask->getQuoteId(),
            $checked
        );
    }
}
