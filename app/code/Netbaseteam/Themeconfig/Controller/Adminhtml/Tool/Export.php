<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Tool;

class Export extends \Magento\Framework\App\Action\Action {
	protected $resultRawFactory;
	protected $fileFactory;
	protected $_helper;
	protected $_directory_list;
	protected $_resultJsonFactory;

	public function __construct(
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		\Netbaseteam\Themeconfig\Helper\Data $helper,
		\Magento\Framework\App\Filesystem\DirectoryList $directory_list,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Backend\App\Action\Context $context
	) {
		$this->resultRawFactory = $resultRawFactory;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->fileFactory = $fileFactory;
		$this->_helper = $helper;
		$this->_directory_list = $directory_list;
		parent::__construct($context);
	}
	public function execute() {
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::tool_export')) {
			$ret = $temp = array();
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}

			$arr_config = json_decode($header_config, true);
			$get_data = $this->getRequest()->getPost();
			$entity_type = $get_data['entity_type'];
			if (isset($entity_type) && count($arr_config) > 0) {
				if ($entity_type == 0) {
					$ret['file'] = 'layouts';
					foreach ($arr_config as $k => $v) {
						if (strpos($k, 'header') !== false || strpos($k, 'content') !== false || strpos($k, 'footer') !== false || strpos($k, 'menu') !== false) {
							$temp += array($k => $v);
						}
					}

					$position = array('content', 'footer', 'header', 'menu', 'main');
					$ret['data'] = $this->_helper->exportConfig($position, $temp);
				} elseif ($entity_type == 1) {
					$ret['file'] = 'menu';
					foreach ($arr_config as $k => $v) {
						if (strpos($k, 'menu') !== false) {
							$temp += array($k => $v);
						}
					}

					$icon_change = $this->_helper->_readFile('Netbaseteam_Themeconfig/css/menu_icon_changed.min.css');
					$temp += array('netbase_icons' => base64_encode(trim($icon_change)));
					$temp += array('netbasekey_icons' => $this->_helper->createKeyString(base64_encode(trim($icon_change))));

					$position = array('menu');
					$ret['data'] = $this->_helper->exportConfig($position, $temp);
				} elseif ($entity_type == 2) {
					$ret['file'] = 'general';
					foreach ($arr_config as $k => $v) {
						if (strpos($k, 'button') !== false) {
							$temp += array($k => $v);
						}
					}

					$position = array('content', 'general');
					$ret['data'] = $this->_helper->exportConfig($position, $temp);
				}

				$file = 'Netbaseteam_Themeconfig/config/' . $ret['file'] . '.json';
				$this->_helper->_writeContent2File($file, json_encode($ret['data']), 'Netbaseteam_Themeconfig/config');

				try {
					$path = $this->_helper->_getUrlFile($file);
					$pub_dir = $this->_directory_list->getPath('pub');
					$path_after_pub = explode("adminhtml", $path);
					$file_path = $pub_dir . "/static/adminhtml" . $path_after_pub[1];
					$fileName = basename($file_path);

					$this->fileFactory->create(
						$fileName,
						@file_get_contents($file_path)
					);
				} catch (\Exception $exception) {
					var_dump($exception->getMessage());
					exit;
				}
			}
		}
		$resultRaw = $this->resultRawFactory->create();
		return $resultRaw;

		// $result = $this->_resultJsonFactory->create();
		// return $result->setData($ret);
	}
}