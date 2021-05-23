<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\FeeRepositoryInterface;
use Netbaseteam\Onestepcheckout\Api\Data\FeeInterface;
use Netbaseteam\Onestepcheckout\Model\ResourceModel\Fee as FeeResource;
use Netbaseteam\Onestepcheckout\Model\ResourceModel\Fee\CollectionFactory as FeeCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class FeeRepository implements FeeRepositoryInterface
{
    /**
     * @var FeeResource
     */
    protected $resource;
    /**
     * @var FeeFactory
     */
    protected $feeFactory;
    /**
     * @var FeeCollectionFactory
     */
    protected $feeCollectionFactory;

    public function __construct(
        FeeResource $resource,
        FeeFactory $feeFactory,
        FeeCollectionFactory $feeCollectionFactory
    ) {

        $this->resource = $resource;
        $this->feeFactory = $feeFactory;
        $this->feeCollectionFactory = $feeCollectionFactory;
    }

    /**
     * @param FeeInterface $fee
     *
     * @return FeeInterface
     * @throws CouldNotSaveException
     */
    public function save(FeeInterface $fee)
    {
        try {
            $this->resource->save($fee);

        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the fee: %1',
                    $exception->getMessage()
                )
            );
        }
        return $fee;
    }

    /**
     * @return Fee
     */
    public function create()
    {
        return $this->feeFactory->create();
    }

    /**
     * @param $feeId
     *
     * @return FeeInterface
     * @throws NoSuchEntityException
     */
    public function getById($feeId)
    {
        $fee = $this->create();
        $this->resource->load($fee, $feeId);

        if (!$fee->getId()) {
            throw new NoSuchEntityException(__('Fee with id "%1" does not exist.', $feeId));
        }
        return $fee;
    }

    /**
     * @param FeeInterface $fee
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(FeeInterface $fee)
    {
        try {
            $this->resource->delete($fee);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the fee: %1',
                    $exception->getMessage()
                )
            );
        }
        return true;
    }

    /**
     * @param $feeId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($feeId)
    {
        return $this->delete($this->getById($feeId));
    }

    /**
     * @param int $quoteId
     *
     * @return FeeInterface
     */
    public function getByQuoteId($quoteId)
    {
        return $this->getByField($quoteId, 'quote_id');
    }

    /**
     * @param int $orderId
     *
     * @return FeeInterface
     */
    public function getByOrderId($orderId)
    {
        return $this->getByField($orderId, 'order_id');
    }

    public function getByField($id, $field)
    {
        /** @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Fee\Collection $feeCollection */
        $feeCollection = $this->feeCollectionFactory->create();

        $feeCollection
            ->addFieldToFilter($field, $id)
            ->setPageSize(1)
        ;

        return $feeCollection->getFirstItem();
    }
}
