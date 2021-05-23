<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model\Config\Source;

class DisplayAgreements implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'payment_method', 'label' => __('Below the selected payment method')],
            ['value' => 'order_totals', 'label' => __('Below the Order Total')]
        ];
    }
}
