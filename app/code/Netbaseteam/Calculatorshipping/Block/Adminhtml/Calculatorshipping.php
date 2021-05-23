<?php
/**
 * Adminhtml calculatorshipping list block
 *
 */
namespace Netbaseteam\Calculatorshipping\Block\Adminhtml;

class Calculatorshipping extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_calculatorshipping';
        $this->_blockGroup = 'Netbaseteam_Calculatorshipping';
        $this->_headerText = __('Calculatorshipping');
        $this->_addButtonLabel = __('Add New Calculatorshipping');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Calculatorshipping::save')) {
            $this->buttonList->update('add', 'label', __('Add New Calculatorshipping'));
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
