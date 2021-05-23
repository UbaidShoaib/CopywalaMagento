<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Main;

use Magento\Framework\View\Result\PageFactory;

class Uploadavt extends \Magento\Framework\App\Action\Action {
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::main_area')) {
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}
			$arr_config = json_decode($header_config, true);

			$arr_config['cb_use_default_logo'] = 0;

			$content_css_org = "";

			$uploadHelper = $this->_objectManager->get('\Netbaseteam\Themeconfig\Helper\Data');

			$base_dir = $uploadHelper->getBaseDir();
			if (!file_exists($base_dir)) {
				mkdir($base_dir, 0777, true);
			}

			$avt_folder = $uploadHelper->getBaseDir() . "/avanta/";
			if (!file_exists($avt_folder)) {
				mkdir($avt_folder, 0777, true);
			}

			$userData = $uploadHelper->getAdminUserData();
			$admin_acc_folder = $avt_folder . "/" . $userData["username"] . "/";
			if (!file_exists($admin_acc_folder)) {
				mkdir($admin_acc_folder, 0777, true);
			}

			$get_data = $this->getRequest()->getPost();
			$avata_url = $logo_url = "";

			$logo_folder = $uploadHelper->getBaseDir() . "/logo/";
			if (!file_exists($logo_folder)) {
				mkdir($logo_folder, 0777, true);
			}

			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'logo_main']
				);

				$files = glob($logo_folder . '*');
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}

				}

				$file = $uploader->validateFile();

				if (isset($file)) {
					$error = $file["error"];
					if (!is_array($file["name"])) {
						$fileName = $file["name"];
						move_uploaded_file($file["tmp_name"], $logo_folder . $fileName);
					}
					$logo_url = $uploadHelper->resize($fileName, null, null, "Themeconfig/logo/");
				}
			} catch (\Exception $e) {
				$curr_logo_url = $get_data['curr_logo_main'];
				if (isset($curr_logo_url) && $curr_logo_url != '') {
					$logo_url = $curr_logo_url;
				}
			}

			$cb_use_default_logo = $get_data['cb_use_default_logo'];
			if (isset($cb_use_default_logo) && $cb_use_default_logo) {
				$arr_config['cb_use_default_logo'] = 1;

				$files = glob($admin_acc_folder . '*');
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}

				}
				$avata_url = $logo_url;
			} else {
				try {
					$uploader = $this->_objectManager->create(
						'Magento\MediaStorage\Model\File\Uploader',
						['fileId' => 'avanta_main']
					);

					$files = glob($admin_acc_folder . '*');
					foreach ($files as $file) {
						if (is_file($file)) {
							unlink($file);
						}

					}

					$file = $uploader->validateFile();

					if (isset($file)) {
						$error = $file["error"];
						if (!is_array($file["name"])) {
							$fileName = $file["name"];
							move_uploaded_file($file["tmp_name"], $admin_acc_folder . $fileName);
						}
						$url = $uploadHelper->resize($fileName, null, null, "Themeconfig/avanta/" . $userData["username"] . "/");
						$avata_url = $url;
					}
				} catch (\Exception $e) {
					$curr_avata_url = $get_data['curr_avanta_main'];
					if (isset($curr_avata_url) && $curr_avata_url != '') {
						$avata_url = $curr_avata_url;
					}
				}
			}

			$position_logo = $get_data['position_logo'];
			if (isset($position_logo) && $position_logo != '') {
				$arr_config['position_logo_header'] = $position_logo;
				$content_css_org .= ".vertical_menu .menu-wrapper .logo, .horizontal_menu .menu-wrapper .logo {
					text-align: " . $position_logo . ";
				}";
				if ($position_logo == 'center') {
					$content_css_org .= "@media(min-width: 991px) {
						.horizontal_menu .page-header-hgroup {
							width: 100% !important;
							display: block !important;
							text-align: center;
						}
					}";
				} elseif ($position_logo == 'right') {
					$content_css_org .= "@media(min-width: 991px) {
						.horizontal_menu .page-header-hgroup {
							width: 100% !important;
							display: block !important;
							text-align: right;
						}
						body.horizontal_menu .page-header-actions {
							left: 0px !important;
							width: 197px !important;
							right: unset !important;
						}
						.horizontal_menu .admin__action-dropdown-wrap .admin__action-dropdown-menu {
							right: -60px;
						}
						.horizontal_menu .search-global-input, .horizontal_menu .search-global-field .autocomplete-results {
							right: -220px;
							top: 30px;
						}
					}";
				}
			}

			if ($logo_url == '') {
				$files = glob($logo_folder . '*');
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}

				}
			}

			$ext_css = '';

			if ($avata_url == '') {
				$files = glob($admin_acc_folder . '*');
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}

				}

				$content_css_org .= ".admin-user .admin__action-dropdown:before {
					background-image: '';
					content: '\\e901' !important;
					font-size: 3.3rem;
					margin: 0;
				}";
			} else {
				$content_css_org .= ".admin-user .admin__action-dropdown:before {
					content: '' !important;
					background-size: 35px 35px;
					width: 35px;
					height: 35px;
					display: block;
				}";

				$ext_css = ".admin-user .admin__action-dropdown:before {
					background-image: url(" . $avata_url . ");
				}";
			}

			$content_css_merger = $this->_helper->mergerCssConfigFile('main', $content_css_org);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_config), 'Netbaseteam_Themeconfig/config');
			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);

			// $ret["inject"] = $ext_css;
			// $ret["logo"] = $logo_url;
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}