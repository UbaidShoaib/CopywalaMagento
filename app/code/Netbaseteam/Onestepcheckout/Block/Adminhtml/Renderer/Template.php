<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Adminhtml\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Template extends \Magento\Backend\Block\Template implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\Onepage
     */
    protected $helper;

    /**
     * Template constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Netbaseteam\Onestepcheckout\Helper\Onepage $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Netbaseteam\Onestepcheckout\Helper\Onepage $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @var AbstractElement
     */
    protected $_element;

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function isStoreSelected()
    {
        return $this->_request->getParam('store', false) !== false;
    }

    /**
     * @param null $moduleName
     * @return bool
     */
    public function isModuleExist($moduleName = null)
    {
        return $this->helper->isModuleOutputEnabled($moduleName);
    }
}
