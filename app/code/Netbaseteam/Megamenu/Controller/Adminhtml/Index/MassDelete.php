<?php
/**
 * Netbaseteam.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the cmsmart.net license that is
 * available through the world-wide-web at this URL:
 * *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Netbaseteam
 * @package     Netbaseteam_Megamenu
 * @copyright   Copyright (c) Netbaseteam (http://www.cmsmart.net/)
 *
 */

namespace Netbaseteam\Megamenu\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

/**
 * Action MassDelete
 */
class MassDelete extends \Netbaseteam\Megamenu\Controller\Adminhtml\Index
{
    /**
     * Execute action
     */
    public function execute()
    {
        $entityIds = $this->getRequest()->getParam('megamenus');
        if (!is_array($entityIds) || empty($entityIds)) {
            $this->messageManager->addError(__('Please select record(s).'));
        } else {
            /** @var \Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\Collection $collection */

            $collection = $this->_objectManager->create('Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\Collection');
            $collection->addFieldToFilter('megamenu_id', ['in' => $entityIds]);
            try {
                $item = 0;
                foreach ($collection as $item) {
                    $item->delete();
                    $item++;
                }

                $this->_typeListInterface->cleanType('block_html');

                $this->_typeListInterface->cleanType('full_page');

                $this->messageManager->addSuccess(
                    __('A total of %1 item(s) have been deleted.', count($entityIds))
                );
            } catch (\Exception $e) {

                $this->messageManager->addError($e->getMessage());

            }
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
