<?php

namespace Netbaseteam\PricingOption\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbaseteam_PricingOption::pricing_option';
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Netbaseteam\PricingOption\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Netbaseteam\PricingOption\Model\ResourceModel\Pricingoptionstore\CollectionFactory $pricingoptionstoreCollectionFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
        $this->pricingoptionstoreCollectionFactory = $pricingoptionstoreCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_PricingOption::option'
        )->addBreadcrumb(
            __('Option'),
            __('Option')
        )->addBreadcrumb(
            __('Manage Seller Option'),
            __('Manage Seller Option')
        );
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Netbaseteam\PricingOption\Model\Option');
        $modelStore = $this->pricingoptionstoreCollectionFactory->create();
        $store = $this->getRequest()->getParam('store') ? $this->getRequest()->getParam('store') : 0;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This option no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if ($id) {
            $modelStore = $modelStore->addFieldToFilter('option_id', $id)->addFieldToFilter('store_id',$store)->getFirstItem();
            if ($modelStore->getId()) {
                if ($modelStore->getFields()) {
                    $model->setFields($modelStore->getFields());
                }
            }
        }

        $this->_coreRegistry->register('current_option', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Option') : __('New Option'),
            $id ? __('Edit Option') : __('New Option')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Option'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getStoreName() : __('New Option'));
        return $resultPage;
    }
}
