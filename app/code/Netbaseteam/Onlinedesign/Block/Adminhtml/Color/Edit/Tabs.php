<?php
namespace Netbaseteam\Onlinedesign\Block\Adminhtml\Color\Edit;

/**
 * Admin onlinedesign left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Color Information'));
    }
}
