<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\PricingOption\Controller\Adminhtml\Option;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbaseteam_PricingOption::pricing_option';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Netbaseteam\PricingOption\Helper\Data
     */
    protected $_helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Netbaseteam\PricingOption\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Netbaseteam\PricingOption\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Netbaseteam_PricingOption::pricingoption_option');
        $resultPage->addBreadcrumb(__('Manage Option'), __('Manage Option'));
        $resultPage->addBreadcrumb(__('Pricing Option'), __('Pricing Option'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Option'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
