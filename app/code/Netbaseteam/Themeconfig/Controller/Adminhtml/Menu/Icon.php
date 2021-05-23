<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Menu;

use Magento\Framework\View\Result\PageFactory;

class Icon extends \Magento\Framework\App\Action\Action {
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
		$new_arr = array();
		$parent_class = ".admin__menu";
		$content_css_org = "";
		$ret = array();

		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::menu_icon')) {
			$content = $this->getRequest()->getParam('content');
			$class = $this->getRequest()->getParam('class');

			$content_in_file = $this->_helper->_readFile('Netbaseteam_Themeconfig/css/menu_icon_changed.min.css');
			$content_arr = explode($parent_class, $content_in_file);
			for ($i = 0; $i < count($content_arr); $i++) {
				if (strpos($content_arr[$i], $class) !== false) {
					continue;
				} else {
					$new_arr[] = $content_arr[$i];
				}
			}

			for ($i = 0; $i < count($new_arr); $i++) {
				if (isset($new_arr[$i]) && trim($new_arr[$i]) != "") {
					$content_css_org .= $parent_class . $new_arr[$i];
				}
			}
			$content_css_org .= '.admin__menu .' . $class . '>a:before {content: ' . "'\\" . $content . '\' !important;}' . "\n";
			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/menu_icon_changed.min.css', $content_css_org);
		}

		$ret["inject"] = $content_css_org;
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}
