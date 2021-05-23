<?php
namespace Netbaseteam\Themeconfig\Block\Adminhtml\Tool;

/**
 * Adminhtml Themeconfig grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Netbaseteam\Themeconfig\Model\Themeconfig
     */
    protected $_themeconfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Netbaseteam\Themeconfig\Model\Themeconfig $themeconfigPage
     * @param \Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Netbaseteam\Themeconfig\Model\Themeconfig $themeconfig,
        \Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_themeconfig = $themeconfig;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netbaseteam_Themeconfig::tool.phtml');
    }
}
