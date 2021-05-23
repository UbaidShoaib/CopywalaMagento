<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */

namespace Netbaseteam\Onestepcheckout\Controller\Adminhtml\Field;

use Netbaseteam\Onestepcheckout\Controller\Adminhtml\Field as FieldAction;
use Magento\Backend\App\Action\Context;

class Save extends FieldAction
{
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field
     */
    protected $fieldResource;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\FieldFactory
     */
    protected $fieldFactory;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store
     */
    protected $storeResource;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store\CollectionFactory
     */
    protected $storeCollectionFactory;

    public function __construct(
        Context $context,
        \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field $fieldResource,
        \Netbaseteam\Onestepcheckout\Model\FieldFactory $fieldFactory,
        \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store $storeResource,
        \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store\CollectionFactory $storeCollectionFactory
    ) {
        parent::__construct($context);
        $this->fieldResource = $fieldResource;
        $this->fieldFactory = $fieldFactory;
        $this->storeResource = $storeResource;
        $this->storeCollectionFactory = $storeCollectionFactory;
    }

    public function execute()
    {
        $fields = $this->_request->getParam('field');

        if (!is_array($fields)) {
            return $this->_redirect('*/*', ['_current' => true]);
        }

        try {
            $this->fieldResource->beginTransaction();
            $store = $this->_request->getParam('store', null);

            foreach ($fields as $attributeId => $fieldData) {
                /** @var \Netbaseteam\Onestepcheckout\Model\Field $field */
                $field = $this->fieldFactory->create();

                // TODO add support for new fields
                $fieldId = $fieldData['id'];

                $this->fieldResource->load($field, $fieldId);

                if (!isset($fieldData['required'])) {
                    $fieldData['required'] = 0;
                }

                $field->addData(array_intersect_key($fieldData, array_flip([
                    'sort_order', 'enabled', 'width', 'required'
                ])));

                if ($store === null) {
                    $field->setData('label', $fieldData['label']);
                }
                else {
                    /** @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Field\Store\Collection $storeCollection */
                    $storeCollection = $this->storeCollectionFactory->create();

                    $fieldStore = $storeCollection
                        ->addFieldToFilter('store_id', $store)
                        ->addFieldToFilter('field_id', $fieldId)
                        ->getFirstItem()
                    ;

                    if (isset($fieldData['label'])) { // Create, Update
                        $fieldStore->addData([
                            'store_id' => $store,
                            'field_id' => $fieldId,
                            'label' => $fieldData['label']
                        ]);

                        $this->storeResource->save($fieldStore);
                    }
                    else { // Delete
                        if ($fieldStore->getId()) {
                            $this->storeResource->delete($fieldStore);
                        }
                    }
                }

                $this->fieldResource->save($field);
            }

            $this->fieldResource->commit();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->fieldResource->rollBack();

            return $this->_redirect('*/*', ['_current' => true]);
        }

        $this->messageManager->addSuccessMessage(__('Fields information has been successfully updated'));

        return $this->_redirect('*/*', ['_current' => true]);
    }
}
