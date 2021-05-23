<?php

namespace Netbaseteam\PricingOption\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;

class Ajax extends \Magento\Backend\App\Action
{
    protected $_helperData;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Netbaseteam\PricingOption\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\Filesystem\Io\File $file
    ) {
        $this->_helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->_jsonHelper = $jsonHelper;
        $this->_fileSystem = $fileSystem;
        $this->file = $file;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_PricingOption::pricing_option');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $action = $this->getRequest()->getParam('action');
        $post = $this->getRequest()->getPostValue();
        if ($action == 'nbd_get_media_full_size_url') {
            $result = array(
                'flag'  =>  1,
                'images'  =>  array()
            );
            $images = $this->_jsonHelper->jsonDecode( stripslashes( $post['images'] ), true );
            foreach( $images as $key => $image ){
                $result['images'][$key] = $image;
            }
        } elseif ($action == 'nbd_download_option_image') {
            $result = array(
                'flag'  =>  1,
                'image'  =>  array()
            );
            $url = $post['image'];

            if( strpos( $url, $this->storeManager->getStore()->getBaseUrl()) === false ){
                if (!$this->_fileSystem->isExists($this->_helperData->getBaseUploadDir().'/'.'pricing_options')) {
                    $this->_fileSystem->createDirectory($this->_helperData->getBaseUploadDir().'/'.'pricing_options', 0775, true);
                }
                $this->nbd_download_remote_file($url, $this->_helperData->getBaseUploadDir().'/'.'pricing_options/'.$this->file->getPathInfo($url)['basename']);
                $result['image'] = array(
                        'current_site'  => 0,
                        'id'  => $this->_helperData->getBaseUrlUpload().'/'.'pricing_options/'.$this->file->getPathInfo($url)['basename']
                    );
            }else{
                $result['image'] = array(
                    'current_site'  => 1
                );
            }
        }
        
        $this->getResponse()->setBody($this->_jsonHelper->jsonEncode($result));
    }

    public function nbd_download_remote_file( $url, $path ){
        $remoteData = array(
            'fileName' => $path,
            'fileData' => base64_encode($path)
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $remoteData);

        $data = curl_exec ($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] == 200){
            if( $data ){
                $file = $this->_fileSystem->fileOpen($path, "w+");
                fputs($file, $data);
                $this->_fileSystem->fileClose($file);
                return true;
            }
            return false;
        }
        curl_close ($curl);
        return false;

    }
}
