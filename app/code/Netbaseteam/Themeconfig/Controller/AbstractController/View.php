<?php

namespace Netbaseteam\Themeconfig\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Netbaseteam\Themeconfig\Controller\AbstractController\ThemeconfigLoaderInterface
     */
    protected $themeconfigLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, ThemeconfigLoaderInterface $themeconfigLoader, PageFactory $resultPageFactory)
    {
        $this->themeconfigLoader = $themeconfigLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Themeconfig view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->themeconfigLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
