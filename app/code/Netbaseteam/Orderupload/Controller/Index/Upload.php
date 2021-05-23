<?php

namespace Netbaseteam\Orderupload\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_objectManagerr;
    protected $_catalogSession;

    protected $_resultJsonFactory;
    protected $_fileUploaderFactory;

    protected $_filesystem;
    protected $_imageFactory;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_catalogSession = $catalogSession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        parent::__construct($context);
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Default Orderupload Index page
     *
     * @return void
     */
    public function execute()
    {
        // server should keep session data for AT LEAST 3 hour
        //ini_set('session.gc_maxlifetime', 10800);

        $session_id = session_id();
        if (empty($session_id)) session_start();

        $uploadHelper = $this->_objectManagerr->get('\Netbaseteam\Orderupload\Helper\Data');
        $pid = $this->getRequest()->getParam('p');

        $base_dir = $uploadHelper->getBaseDir();
        if (!file_exists($base_dir)) {
            mkdir($base_dir, 0777);
        }

        $output_dir = $uploadHelper->getBaseDir() . "/" . $pid . "/";

        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0777);
        }

        $uploader = $this->_objectManagerr->create(
            'Magento\MediaStorage\Model\File\Uploader',
            ['fileId' => 'myfile']
        );

        $file = $uploader->validateFile();

        if (isset($file)) {
            $ret = array();
            if (!is_array($file["name"])) {
                $fileName = $this->changeImageName($file["name"]);//$file["name"];
                move_uploaded_file($file["tmp_name"], $output_dir . $fileName);
                $ret[] = $fileName;
                $jsonFile = $output_dir . $session_id . '.json';
                if (!file_exists($jsonFile)) {
                    file_put_contents($jsonFile, "[]");
                }
                $tmp = array();
                $inp = file_get_contents($jsonFile);
                $tempArray = json_decode($inp);
                if (@is_array(getimagesize($absolutePath = $output_dir . $fileName))) {
                    $outputDirResize = $uploadHelper->getBaseDir() . "/" . $pid . "/resize/60/";
                    $imageResized = $outputDirResize . $fileName;
                    //create image factory
                    $imageResize = $this->_imageFactory->create();
                    $imageResize->open($absolutePath);
                    $imageResize->constrainOnly(TRUE);
                    $imageResize->keepTransparency(TRUE);
                    $imageResize->keepFrame(FALSE);
                    $imageResize->keepAspectRatio(TRUE);
                    $imageResize->resize(60, 60);
                    //destination folder
                    $destination = $imageResized;
                    //save image
                    $imageResize->save($destination);
                }

                $data['order_id'] = "";
                $data['file'] = $pid . "/" . $fileName;
                $data['parent_pid'] = $pid;
                $data['child_pid'] = "";
                $data['comment'] = "";
                $data["child_sku"] = "";
                $tmp[] = $data;

                array_push($tempArray, $tmp);
                $jsonData = json_encode($tempArray);
                file_put_contents($jsonFile, $jsonData);

            } else {
                $fileCount = count($file["name"]);
                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = $file["name"][$i];
                    move_uploaded_file($file["tmp_name"][$i], $output_dir . $fileName);
                    $ret[] = $fileName;
                }
            }
            $result = $this->_resultJsonFactory->create();
            return $result->setData($ret);
        }
    }

     protected function changeImageName($fileName) {
        $arr = array(
            'a' => 'à|ả|ã|á|ạ|ă|ằ|ẳ|ẵ|ắ|ặ|â|ầ|ẩ|ẫ|ấ|ậ',
            'd' => 'đ',
            'e' => 'è|ẻ|ẽ|é|ẹ|ê|ề|ể|ễ|ế|ệ',
            'i' => 'ì|ỉ|ĩ|í|ị',
            'o' => 'ò|ỏ|õ|ó|ọ|ô|ồ|ổ|ỗ|ố|ộ|ơ|ờ|ở|ỡ|ớ|ợ',
            'u' => 'ù|ủ|ũ|ú|ụ|ư|ừ|ử|ữ|ứ|ự',
            'y' => 'ỳ|ỷ|ỹ|ý|ỵ',
        );
        $newValue = trim($fileName);
        foreach ($arr as $key => $val) {
            $pattern = '#(' . $val . ')#imu';
            $newValue = preg_replace($pattern, $key, $newValue);
        }
        $newValue = preg_replace('#(\s)+#im', '-', $newValue);
        $newValue = str_replace('-', '_', $newValue);
        $newValue = str_replace(' ', '_', $newValue);
        return $newValue;
    }
}
