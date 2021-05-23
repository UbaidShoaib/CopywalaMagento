<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\General;

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
        return $this->_authorization->isAllowed('Netbaseteam_Themeconfig::general_manage');
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
            'Netbaseteam_Themeconfig::general_manage'
        )->addBreadcrumb(
            __('General Settings'),
            __('General Settings')
        )->addBreadcrumb(
            __('General Settings'),
            __('General Settings')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('General Settings'));
        return $resultPage;
    }
}
