<?php
/**
 * @category   Netbaseteam
 * @package    Netbaseteam_Ajaxcart
 * @copyright  Copyright (c) 2018 Netbaseteam
 */

namespace Netbaseteam\Ajaxcart\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Netbaseteam\Ajaxcart\Controller\AbstractController\AjaxcartLoaderInterface
     */
    protected $ajaxcartLoader;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param AjaxcartLoaderInterface $ajaxcartLoader
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, AjaxcartLoaderInterface $ajaxcartLoader, PageFactory $resultPageFactory)
    {
        $this->ajaxcartLoader = $ajaxcartLoader;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Ajaxcart view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->ajaxcartLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
