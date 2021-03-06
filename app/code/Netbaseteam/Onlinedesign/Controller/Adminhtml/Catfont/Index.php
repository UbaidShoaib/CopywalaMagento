<?php

namespace Netbaseteam\Onlinedesign\Controller\Adminhtml\Catfont;

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
        return $this->_authorization->isAllowed('Netbaseteam_Onlinedesign::onlinedesign_catcustomfont');
    }

    /**
     * Onlinedesign List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_Onlinedesign::onlinedesign_catcustomfont'
        )->addBreadcrumb(
            __('Category Font'),
            __('Category Font')
        )->addBreadcrumb(
            __('Manage Category Font'),
            __('Manage Category Font')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Category Font'));
        return $resultPage;
    }
}
