<?php
namespace Netbaseteam\Blog\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Netbaseteam\Blog\Model\ResourceModel\Comment\CollectionFactory;

class MassStatus extends \Magento\Backend\App\Action
{
   
    protected $_productPriceIndexerProcessor;

    
    protected $filter;

    protected $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

   

    public function execute()
    {
        
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $commentIds = $collection->getAllIds();
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $status = (int) $this->getRequest()->getParam('status');

        try {
            foreach ($collection as $item) {
               $item->setStatus($status);
               $item->save();
            }

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', count($commentIds)));
            
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the Comment(s) status.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*');
    }
}
