<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model\Sales\Pdf;

use Netbaseteam\Onestepcheckout\Api\FeeRepositoryInterface;
use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;

class GiftWrap extends DefaultTotal
{
    /**
     * @var FeeRepositoryInterface
     */
    protected $feeRepository;

    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        FeeRepositoryInterface $feeRepository,
        array $data = []
    ) {

        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
        $this->feeRepository = $feeRepository;
    }

    public function getAmount()
    {
        $fee = $this->feeRepository->getByOrderId($this->getSource()->getOrderId());

        if (!$fee)
            return 0;

        return $fee->getAmount();
    }
}
