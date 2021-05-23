<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Main;

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
        return $this->_authorization->isAllowed('Netbaseteam_Themeconfig::main_manage');
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
            'Netbaseteam_Themeconfig::main_manage'
        )->addBreadcrumb(
            __('Main Area'),
            __('Main Area')
        )->addBreadcrumb(
            __('Manage Main Area'),
            __('Manage Main Area')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Main Area'));
        return $resultPage;
    }
}
