<?php
/**
 * Adminhtml themeconfig list block
 *
 */
namespace Netbaseteam\Themeconfig\Block\Adminhtml;

class Main extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_main';
        $this->_blockGroup = 'Netbaseteam_Themeconfig';
        $this->_headerText = __('Themeconfig');
        $this->_addButtonLabel = __('Add New Themeconfig');
        parent::_construct();
		
		$this->buttonList->remove('add');
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
