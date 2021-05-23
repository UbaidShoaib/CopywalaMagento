<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Tool;

use Magento\Framework\View\Result\PageFactory;

class Import extends \Magento\Framework\App\Action\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	protected $_resultJsonFactory;
	protected $_helper;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		PageFactory $resultPageFactory,
		\Netbaseteam\Themeconfig\Helper\Data $helper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_helper = $helper;
		parent::__construct($context);
	}

	public function getCurrentProduct() {
		return $this->_registry->registry('current_product');
	}

	/**
	 * Default Orderupload Index page
	 *
	 * @return void
	 */
	public function execute() {
		$ret = array();
		$ret["message"] = "";
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::tool_import')) {
			$uploadHelper = $this->_objectManager->get('\Netbaseteam\Themeconfig\Helper\Data');

			$base_dir = $uploadHelper->getBaseDir();
			if (!file_exists($base_dir)) {
				mkdir($base_dir, 0777);
			}

			$temp_folder = $uploadHelper->getBaseDir() . "/temp/";

			if (!file_exists($temp_folder)) {
				mkdir($temp_folder, 0777);
			}

			$get_data = $this->getRequest()->getPost();
			//delete all old files
			$files = glob($temp_folder . '*'); // get all file names
			foreach ($files as $file) {
				// iterate files
				if (is_file($file)) {
					unlink($file);
				}
				// delete file
			}

			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/huan.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);

			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'userImportConfig']
				);

				$uploader->setAllowedExtensions(['json']);

				$file = $uploader->validateFile();

				$ret["file_name"] = "";
				if (isset($file)) {
					$ret = array();
					$error = $file["error"];
					if (!is_array($file["name"])) {
						$fileName = $file["name"];
						move_uploaded_file($file["tmp_name"], $temp_folder . $fileName);
					}
				}
				$file_custom_config = file_get_contents($temp_folder . $fileName);

				$arr_temp = json_decode($file_custom_config, true);

				$default_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
				if (!$this->_helper->isJson($default_config)) {
					$default_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
				}
				$arr_default_config = json_decode($default_config, true);

				foreach ($arr_temp as $key => $value) {
					if (strpos($key, 'netbasekey_') == false) {
						if (strpos($key, 'netbase_') !== false && $value != '') {
							if ($key == 'netbase_icons' && array_key_exists("netbasekey_icons", $arr_temp)) {
								if ($arr_temp['netbasekey_icons'] == $this->_helper->createKeyString($value)) {
									$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/menu_icon_changed.min.css', base64_decode($value));
								}
							} else {
								$arr_nd = explode('_', $key);
								if (count($arr_nd) > 0) {
									if (array_key_exists("netbasekey_" . $arr_nd[1], $arr_temp)) {
										if ($arr_temp['netbasekey_' . $arr_nd[1]] == $this->_helper->createKeyString($value)) {
											$content_css_merger = $this->_helper->mergerCssConfigFile($arr_nd[1], base64_decode($value));
											$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);
										}
									}
								}
							}
						} else {
							$arr_default_config[$key] = $value;
						}
					}
				}
				$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_default_config), 'Netbaseteam_Themeconfig/config');
			} catch (\Exception $e) {
				$logger->info($e->getMessage());
				$ret["message"] = $e->getMessage();
			}
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}
