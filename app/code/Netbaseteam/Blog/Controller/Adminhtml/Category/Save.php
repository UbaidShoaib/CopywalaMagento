<?php

namespace Netbaseteam\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    protected $_urlRewriteFactory;

    protected $_dataHelper;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper
    ){
        $this->dataProcessor = $dataProcessor;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Blog::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            
            $data = $this->dataProcessor->filter($data);

            if (!isset($data['post_ids'])) {
                if ($data['post_ids_callback']==''){
                    $this->messageManager->addError(__('Please select Posts for this Category in Select Post tab'));
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', ['blog_category_id' => $this->getRequest()->getParam('blog_category_id')]);
                    return;
                }  
            }else{
                if ($data['post_ids']==''){
                    $this->messageManager->addError(__('Please select Posts for this Category in Select Post tab'));
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', ['blog_category_id' => $this->getRequest()->getParam('blog_category_id')]);
                    return;
                }  
            }
            
            $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Category');
            $id = $this->getRequest()->getParam('blog_category_id');
            if ($id) {
                $model->load($id);
            }
            
            if(!empty($data['store_ids'])){
                if(in_array('0',$data['store_ids'])){
                    $data['store_ids'] = '0';
                }else{
                    $data['store_ids'] = implode(",", $data['store_ids']);    
                }          
            }
            
            if (isset($data['category_image'])) {
                $imageData = $data['category_image'];
                unset($data['category_image']);
            } else {
                $imageData = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['blog_category_id' => $model->getBlogCategoryId(), '_current' => true]);
                return;
            }

            try {
                
                $imageHelper = $this->_objectManager->get('Netbaseteam\Blog\Helper\Data');

                if (isset($imageData['delete']) && $model->getCategoryImage()) {
                    $imageHelper->removeImage($model->getCategoryImage(),'category_image');
                    $model->setImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('category_image');
                if ($imageFile) {
                    $model->setCategoryImage($imageFile);
                }

                $model->save();

                $categoryId = $model->getId();
                //if(empty($data['url_rewrite_id'])){
                     $urlRewriteId = $this->createUrlRewrites($data['identifier'],$categoryId);
                    $model->load($categoryId)->setUrlRewriteId($urlRewriteId);
                    $model->save();
                // }else{
                //     $this->updateUrlRewrite($data['identifier'],$data['url_rewrite_id']);
                // }
                
                
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['blog_category_id' => $model->getBlogCategoryId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['blog_category_id' => $this->getRequest()->getParam('blog_category_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }


    public function createUrlRewrites($identifier,$categoryId){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();

                $storeIds = $this->getAllStoreIds();
                 $urlRewriteIds = [];
                
                foreach ($storeIds as $storeId) {
                    $requestPath = 'blog/'.$identifier;
                    $targetPath = 'blog/category/index/blog_category_id/'.$categoryId;
                    $data = array(
                        'url_rewrite_id'=>null,
                        'entity_type'=>'blog-category',
                        'entity_id' =>$categoryId,
                        'request_path'=>$requestPath,
                        'target_path'=>$targetPath,
                        'store_id'=>$storeId
                    );
                    $urlRewriteModel = $this->_urlRewriteFactory->create();
                    $urlRewriteModel->addData($data);
                    $urlRewriteModel->save();
                    
                    $urlRewriteId = $urlRewriteModel->getId();
                    $urlRewriteIds[] =  $urlRewriteId;
                }

                
                
                $ids = implode("&",$urlRewriteIds);
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                
                return $ids;

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Category');
                $model->load($categoryId)->delete();
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this category'));
            }
        
    }

    public function updateUrlRewrite($identifier,$urlRewriteIds){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();
               
                $requestPath = 'blog/'.$identifier;
                 $urlRewriteIds = explode("&", $urlRewriteIds);
                foreach ($urlRewriteIds as $urlRewriteId) {
                    $data = array(
                        'url_rewrite_id'=>$urlRewriteId,
                        'request_path'=>$requestPath
                    );
                    $urlRewriteModel = $this->_urlRewriteFactory->create();
                    $urlRewriteModel->addData($data);
                    $urlRewriteModel->save();
                }
                
                $this->messageManager->addSuccess(__('The Data has been saved.'));

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this category'));
            }
        
    }

    public function getAllStoreIds(){
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $stores = $storeManager->getStores($withDefault = false);
        $storeIds = [];
        foreach($stores as $store) {
            $storeIds[] = $store->getStoreId();
        }

        return $storeIds;

    }   


}
