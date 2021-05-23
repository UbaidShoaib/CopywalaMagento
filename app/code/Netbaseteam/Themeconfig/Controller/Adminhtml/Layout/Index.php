<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Layout;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Themeconfig::layout_manage');
    }

    /**
     * Themeconfig List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_Themeconfig::layout_manage'
        )->addBreadcrumb(
            __('Admin Theme Layouts'),
            __('Admin Theme Layouts')
        )->addBreadcrumb(
            __('Admin Theme Layouts'),
            __('Admin Theme Layouts')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Admin Theme Layouts'));
        return $resultPage;
    }
}
