<?php

namespace Netbaseteam\Calculatorshipping\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Netbaseteam_Calculatorshipping::calculatorshipping_manage');
    }

    /**
     * Calculatorshipping List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_Calculatorshipping::calculatorshipping_manage'
        )->addBreadcrumb(
            __('Calculatorshipping'),
            __('Calculatorshipping')
        )->addBreadcrumb(
            __('Manage Calculatorshipping'),
            __('Manage Calculatorshipping')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Calculatorshipping'));
        return $resultPage;
    }
}
