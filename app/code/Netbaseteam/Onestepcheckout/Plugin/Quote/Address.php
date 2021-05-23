<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin\Quote;

class Address
{
    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\Address
     */
    protected $addressHelper;

    public function __construct(
        \Netbaseteam\Onestepcheckout\Helper\Address $addressHelper
    ) {
        $this->addressHelper = $addressHelper;
    }

    public function afterAddData(
        \Magento\Quote\Model\Quote\Address $subject,
        $result
    ) {
        $this->addressHelper->fillEmpty($subject);

        return $result;
    }
}
