<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\DeliveryInformationManagementInterface;

class DeliveryInformationManagement implements DeliveryInformationManagementInterface
{
    /**
     * @var ResourceModel\Delivery
     */
    protected $deliveryResource;

    /**
     * @var Delivery
     */
    protected $delivery;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        \Netbaseteam\Onestepcheckout\Model\ResourceModel\Delivery $deliveryResource,
        \Netbaseteam\Onestepcheckout\Model\Delivery $delivery,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->deliveryResource = $deliveryResource;
        $this->delivery = $delivery;
        $this->escaper = $escaper;
    }

    /**
     * @param int $cartId
     * @param string $date
     * @param int $time
     * @param string $comment
     * @return bool
     */
    public function update($cartId, $date, $time, $comment)
    {
        $delivery = $this->delivery->findByQuoteId($cartId);

        $delivery->addData([
            'date' => strtotime($date) ?: null,
            'time' => $time >= 0 ? $time : null,
            'comment' => ($comment) ?: $this->escaper->escapeHtml($comment)
        ]);

        if ($delivery->getData('date') === null && $delivery->getData('time') === null && $delivery->getData('comment') === null) {
            if ($delivery->getId()) {
                $this->deliveryResource->delete($delivery);
            }
        }
        else {
            $this->deliveryResource->save($delivery);
        }

        return true;
    }
}
