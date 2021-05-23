<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Api\Data;

interface FeeInterface
{
    const ENTITY_ID = 'id';
    const ORDER_ID = 'order_id';
    const QUOTE_ID = 'quote_id';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @return int|null
     */
    public function getQuoteId();

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return int
     */
    public function getBaseAmount();

    /**
     * @param int $id
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setOrderId($id);

    /**
     * @param int $id
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setQuoteId($id);

    /**
     * @param int $amount
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setAmount($amount);

    /**
     * @param int $amount
     * @return \Netbaseteam\Onestepcheckout\Api\Data\FeeInterface
     */
    public function setBaseAmount($amount);
}
