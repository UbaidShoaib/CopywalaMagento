<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Netbaseteam\Onestepcheckout\Api\Data\FeeInterface;
use Magento\Framework\Model\AbstractModel;

class Fee extends AbstractModel implements FeeInterface
{
    protected function _construct()
    {
        $this->_init('Netbaseteam\Onestepcheckout\Model\ResourceModel\Fee');
    }

    /**
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @return int
     */
    public function getBaseAmount()
    {
        return $this->getData(self::BASE_AMOUNT);
    }

    /**
     * @param int $id
     *
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setOrderId($id)
    {
        $this->setData(self::ORDER_ID, $id);

        return $this;
    }

    /**
     * @param int $id
     *
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setQuoteId($id)
    {
        $this->setData(self::QUOTE_ID, $id);

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setAmount($amount)
    {
        $this->setData(self::AMOUNT, $amount);

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setBaseAmount($amount)
    {
        $this->setData(self::AMOUNT, $amount);

        return $this;
    }
}
