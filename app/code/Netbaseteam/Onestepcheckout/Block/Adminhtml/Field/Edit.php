<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Adminhtml\Field;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;

class Edit extends FormContainer
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_field';
        $this->_blockGroup = 'Netbaseteam_Onestepcheckout';

        parent::_construct();

        $this->buttonList->remove('reset');
    }

    public function getHeaderText()
    {
        return __('Manage Checkout Fields');
    }
}
