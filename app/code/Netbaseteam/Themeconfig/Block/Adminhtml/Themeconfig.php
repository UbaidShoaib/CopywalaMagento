<?php
/**
 * Adminhtml themeconfig list block
 *
 */
namespace Netbaseteam\Themeconfig\Block\Adminhtml;

class Themeconfig extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_themeconfig';
        $this->_blockGroup = 'Netbaseteam_Themeconfig';
        $this->_headerText = __('Themeconfig');
        $this->_addButtonLabel = __('Add New Themeconfig');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Themeconfig::save')) {
            $this->buttonList->update('add', 'label', __('Add New Themeconfig'));
        } else {
            $this->buttonList->remove('add');
        }
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
