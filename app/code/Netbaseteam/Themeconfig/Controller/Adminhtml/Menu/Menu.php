<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Menu;

use Magento\Framework\View\Result\PageFactory;

class Menu extends \Magento\Framework\App\Action\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	protected $_resultJsonFactory;
	protected $_configWriter;
	protected $_helper;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Netbaseteam\Themeconfig\Helper\Data $helper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_configWriter = $configWriter;
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::menu_position')) {
			$type = $this->getRequest()->getParam('type');
			if (isset($type)) {
				if ($type == "vertical") {
					$value = 1;
				} elseif ($type == "vertical_df") {
					$value = 2;
				} else {
					$value = 0;
				}
			}
			// $this->_helper->_writeFile('Netbaseteam_Themeconfig/css/h_menu.css', $source_file);
			$this->_configWriter->save('themeconfig/menu/type', $value);

			$ret["message"] = $value;
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}
