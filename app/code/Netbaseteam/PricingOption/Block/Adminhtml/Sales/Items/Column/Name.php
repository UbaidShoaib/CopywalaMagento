<?php
namespace Netbaseteam\PricingOption\Block\Adminhtml\Sales\Items\Column;
 
class Name
{
    public function beforeToHtml(\Magento\Sales\Block\Adminhtml\Items\Column\Name $subject)
    {
        $subject->setTemplate('Netbaseteam_PricingOption::sales/items/column/name.phtml');
    }
}