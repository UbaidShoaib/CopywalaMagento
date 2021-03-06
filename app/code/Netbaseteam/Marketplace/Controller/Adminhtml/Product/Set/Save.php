<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Product\Set;

class Save extends \Magento\Catalog\Controller\Adminhtml\Product\Set
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Netbaseteam\Marketplace\Model\AttributeFactory $mpAttributeFactory,
        \Netbaseteam\Marketplace\Model\AttributeSetFactory $mpAttributeSetFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mpAttributeFactory = $mpAttributeFactory;
        $this->mpAttributeSetFactory = $mpAttributeSetFactory;
    }

    /**
     * Retrieve catalog product entity type id
     *
     * @return int
     */
    protected function _getEntityTypeId()
    {
        if ($this->_coreRegistry->registry('entityType') === null) {
            $this->_setTypeId();
        }
        return $this->_coreRegistry->registry('entityType');
    }

    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $entityTypeId = $this->_getEntityTypeId();
        $hasError = false;
        $attributeSetId = $this->getRequest()->getParam('id', false);
        $isNewSet = $this->getRequest()->getParam('gotoEdit', false) == '1';

        /* @var $model \Magento\Eav\Model\Entity\Attribute\Set */
        $model = $this->_objectManager->create(\Magento\Eav\Model\Entity\Attribute\Set::class)
            ->setEntityTypeId($entityTypeId);

        /** @var $filterManager \Magento\Framework\Filter\FilterManager */
        $filterManager = $this->_objectManager->get(\Magento\Framework\Filter\FilterManager::class);

        try {
            if ($isNewSet) {
                //filter html tags
                $name = $filterManager->stripTags($this->getRequest()->getParam('attribute_set_name'));
                $model->setAttributeSetName(trim($name));
            } else {
                if ($attributeSetId) {
                    $model->load($attributeSetId);
                }
                if (!$model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('This attribute set no longer exists.')
                    );
                }
                $data = $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)
                    ->jsonDecode($this->getRequest()->getPost('data'));
                $allAttributes = $data['attributes'];
                $allAttributeIds = array();
                $mpAttributeIds = array();
                $listMpAttr = array();
                $mpAttributes = $this->mpAttributeFactory->create()->getCollection();
                if(count($mpAttributes)) {
                    foreach($mpAttributes as $item) {
                        $mpAttributeIds[] = $item->getAttributeId();
                    }
                }
                
                if(count($allAttributes)) {
                    foreach ($allAttributes as $attribute) {
                        if(isset($attribute[0]) && in_array($attribute[0], $mpAttributeIds)) {
                            $listMpAttr[] = $attribute[0];
                        }
                    }
                    $mpModel = $this->mpAttributeSetFactory->create();
                    $mpModelId = $mpModel->getCollection()->addFieldToFilter('attribute_set_id', $this->getRequest()->getParam('id'))->getFirstItem()->getId();
                    if ($mpModelId) {
                        $mpModel->load($mpModelId);
                    }
                    $mpModel->setAttributeSetId($this->getRequest()->getParam('id'));
                    $mpModel->setAttributeId(implode(',', $listMpAttr));
                    $mpModel->save();
                }
                
                //filter html tags
                $data['attribute_set_name'] = $filterManager->stripTags($data['attribute_set_name']);

                $model->organizeData($data);
            }

            $model->validate();
            if ($isNewSet) {
                $model->save();
                $model->initFromSkeleton($this->getRequest()->getParam('skeleton_set'));
            }
            $model->save();
            $this->messageManager->addSuccess(__('You saved the attribute set.'));
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $hasError = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $hasError = true;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the attribute set.'));
            $hasError = true;
        }

        if ($isNewSet) {
            if ($this->getRequest()->getPost('return_session_messages_only')) {
                /** @var $block \Magento\Framework\View\Element\Messages */
                $block = $this->layoutFactory->create()->getMessagesBlock();
                $block->setMessages($this->messageManager->getMessages(true));
                $body = [
                    'messages' => $block->getGroupedHtml(),
                    'error' => $hasError,
                    'id' => $model->getId(),
                ];
                return $this->resultJsonFactory->create()->setData($body);
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                if ($hasError) {
                    $resultRedirect->setPath('catalog/*/add');
                } else {
                    $resultRedirect->setPath('catalog/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect;
            }
        } else {
            $response = [];
            if ($hasError) {
                $layout = $this->layoutFactory->create();
                $layout->initMessages();
                $response['error'] = 1;
                $response['message'] = $layout->getMessagesBlock()->getGroupedHtml();
            } else {
                $response['error'] = 0;
                $response['url'] = $this->getUrl('catalog/*/');
            }
            return $this->resultJsonFactory->create()->setData($response);
        }
    }
}
