<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\PricingOption\Controller\Adminhtml\Option;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Netbaseteam\PricingOption\Controller\Adminhtml\AbstractMassAction;
use Netbaseteam\PricingOption\Model\ResourceModel\Option\CollectionFactory;
use Netbaseteam\PricingOption\Model\OptionFactory;

/**
 * Class MassDelete
 */
class MassStatus extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Netbaseteam_PricingOption::pricing_option';

    /**
     * @var \Netbaseteam\PricingOption\Helper\Data
     */
    protected $_helper;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OptionFactory $manageFactory,
        \Netbaseteam\PricingOption\Helper\Data $pricingoptionHelperData,
        \Magento\Framework\Url $urlBuilder,
        \Netbaseteam\PricingOption\Helper\Data $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->model = $manageFactory;
        $this->_pricingoptionHelperData  = $pricingoptionHelperData;
        $this->urlBuilder = $urlBuilder;
        $this->_helper = $helper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction($collection)
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $status = $this->getRequest()->getParam('published');

        $itemsSelected = 0;
        foreach ($collection->getAllIds() as $itemId) {
            $model = $this->model->create()->load($itemId);
            $model->setPublished($status);
            $model->save();
            $itemsSelected++;
        }

        if ($itemsSelected) {
            $this->messageManager->addSuccess(__('A total of %1 option(s) were updated.', $itemsSelected));
        } else {
            $this->messageManager->addErrorMessage('Something went wrong while update option');
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}