<?php

namespace Netbaseteam\PricingOption\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netbaseteam_PricingOption::pricing_option';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Netbaseteam\PricingOption\Helper\Data
     */
    protected $_helper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Netbaseteam\PricingOption\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $postItems = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        if ($postItems) {

            $id = 0;
            foreach ($postItems as $item) {
                $id = $item['id'];
                $status = $item['published'];
            }

            if ($id) {
                $model = $this->_objectManager->create('Netbaseteam\PricingOption\Model\Option')->load($id);

                $model->setPublished($status);
                $model->save();

            }

            return $resultJson->setData([
                'messages' => [__('Option published has been updated.')],
                'error' => false
            ]);
        }
    }
}
