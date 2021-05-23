<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Block\Sales\Order\Email;

class Delivery extends \Netbaseteam\Onestepcheckout\Block\Sales\Order\Info\Delivery
{
    protected function _construct()
    {
        parent::_construct();

        $this
            ->setTemplate('Netbaseteam_Onestepcheckout::sales/order/email/delivery.phtml')
            ->setData('area', 'frontend')
        ;
    }
}

