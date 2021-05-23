<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Model;

use Magento\Framework\DataObject;

class Totals extends DataObject implements \Netbaseteam\Onestepcheckout\Api\Data\TotalsInterface
{
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    public function getImageData()
    {
        return $this->getData(self::IMAGE_DATA);
    }

    public function getOptionsData()
    {
        return $this->getData(self::OPTIONS_DATA);
    }

    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }
}
