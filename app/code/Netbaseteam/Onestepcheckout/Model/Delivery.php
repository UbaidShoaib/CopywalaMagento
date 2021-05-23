<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Magento\Framework\Model\AbstractModel;

class Delivery extends AbstractModel
{
    /**
     * @var ResourceModel\Delivery\CollectionFactory
     */
    protected $deliveryCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,

        \Netbaseteam\Onestepcheckout\Model\ResourceModel\Delivery\CollectionFactory $deliveryCollectionFactory,

        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->deliveryCollectionFactory = $deliveryCollectionFactory;
    }

    protected function _construct()
    {
        $this->_init('Netbaseteam\Onestepcheckout\Model\ResourceModel\Delivery');
    }

    public function findByQuoteId($quoteId)
    {
        $delivery = $this->findByField($quoteId, 'quote_id');

        if (!$delivery->getId()) {
            $delivery->setData('quote_id', $quoteId);
        }

        return $delivery;
    }

    public function findByOrderId($orderId)
    {
        return $this->findByField($orderId, 'order_id');
    }

    public function findByField($value, $field)
    {
        /** @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Delivery\Collection $deliveryCollection */
        $deliveryCollection = $this->deliveryCollectionFactory->create();

        /** @var \Netbaseteam\Onestepcheckout\Model\Delivery $delivery */
        $delivery = $deliveryCollection
            ->addFieldToFilter($field, $value)
            ->getFirstItem()
        ;

        return $delivery;
    }
}
