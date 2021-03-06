<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\GuestItemManagementInterface;
use Netbaseteam\Onestepcheckout\Api\ItemManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\Data\AddressInterface;

class GuestItemManagement implements GuestItemManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;
    /**
     * @var ItemManagementInterface
     */
    protected $itemManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        ItemManagementInterface $itemManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->itemManagement = $itemManagement;
    }

    public function remove($cartId, $itemId, AddressInterface $address)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->itemManagement->remove(
            $quoteIdMask->getQuoteId(),
            $itemId,
            $address
        );
    }

    public function update($cartId, $itemId, $formData, AddressInterface $address)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->itemManagement->update(
            $quoteIdMask->getQuoteId(),
            $itemId,
            $formData,
            $address
        );
    }
}
