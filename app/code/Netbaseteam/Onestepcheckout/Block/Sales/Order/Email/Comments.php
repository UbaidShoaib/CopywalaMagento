<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Sales\Order\Email;

class Comments extends \Magento\Sales\Block\Order\View
{

    protected function _construct()
    {
        parent::_construct();

        $this
            ->setTemplate('Netbaseteam_Onestepcheckout::onepage/details/comments.phtml')
            ->setData('area', 'frontend')
        ;
    }

    /**
     * @return bool|mixed
     */
    public function getOrder()
    {
        if ($order = $this->getData('order')) {
            return $order;
        }

        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }

        return false;
    }
}

