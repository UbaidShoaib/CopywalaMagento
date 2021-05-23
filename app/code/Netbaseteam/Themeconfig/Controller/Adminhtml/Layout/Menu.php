<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Layout;

use Magento\Framework\View\Result\PageFactory;

class Menu extends \Magento\Framework\App\Action\Action {
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
		\Magento\Catalog\Model\Session $catalogSession,
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::layout_menu')) {
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}
			$arr_config = json_decode($header_config, true);
			$content_css_org = "";
			$after_fix = "_menu";

			$b = $bs = $br = $bp = $bw = $bst = $bc = $wmbs = "";

			$get_data = $this->getRequest()->getPost();

			$bgcolor_menu = $get_data['bgcolor_menu'];
			if (isset($bgcolor_menu) && $bgcolor_menu != '') {
				$b = 'background: ' . $bgcolor_menu . ';';
				$arr_config['background_color' . $after_fix] = $bgcolor_menu;
			}

			$use_border = $get_data['use_border_menu'];
			if (isset($use_border)) {
				$arr_config['use_border' . $after_fix] = $use_border;
				if ($use_border == 'yes') {
					$brtop = $get_data['brtop_menu'];
					$brright = $get_data['brright_menu'];
					$brbottom = $get_data['brbottom_menu'];
					$brleft = $get_data['brleft_menu'];
					if (isset($brleft) && isset($brbottom) && isset($brright) && isset($brtop)) {
						$bw = 'border-width: ' . $brtop . 'px ' . $brright . 'px ' . $brbottom . 'px ' . $brleft . 'px;';
						$arr_config['brtop' . $after_fix] = $brtop;
						$arr_config['brright' . $after_fix] = $brright;
						$arr_config['brbottom' . $after_fix] = $brbottom;
						$arr_config['brleft' . $after_fix] = $brleft;
					}
					$brtop_style_menu = $get_data['brtop_style_menu'];
					$brright_style_menu = $get_data['brright_style_menu'];
					$brbottom_style_menu = $get_data['brbottom_style_menu'];
					$brleft_style_menu = $get_data['brleft_style_menu'];
					if (isset($brtop_style_menu) && isset($brright_style_menu) && isset($brbottom_style_menu) && isset($brleft_style_menu)) {
						$bst = 'border-style: ' . $brtop_style_menu . ' ' . $brright_style_menu . ' ' . $brbottom_style_menu . ' ' . $brleft_style_menu . ';';
						$arr_config['brtop_style' . $after_fix] = $brtop_style_menu;
						$arr_config['brright_style' . $after_fix] = $brright_style_menu;
						$arr_config['brbottom_style' . $after_fix] = $brbottom_style_menu;
						$arr_config['brleft_style' . $after_fix] = $brleft_style_menu;
					}
					$brtop_color_menu = $get_data['brtop_color_menu'];
					$brright_color_menu = $get_data['brright_color_menu'];
					$brbottom_color_menu = $get_data['brbottom_color_menu'];
					$brleft_color_menu = $get_data['brleft_color_menu'];
					if (isset($brtop_color_menu) && isset($brright_color_menu) && isset($brbottom_color_menu) && isset($brleft_color_menu)) {
						$bc = 'border-color: ' . $brtop_color_menu . ' ' . $brright_color_menu . ' ' . $brbottom_color_menu . ' ' . $brleft_color_menu . ';';
						$arr_config['brtop_color' . $after_fix] = $brtop_color_menu;
						$arr_config['brright_color' . $after_fix] = $brright_color_menu;
						$arr_config['brbottom_color' . $after_fix] = $brbottom_color_menu;
						$arr_config['brleft_color' . $after_fix] = $brleft_color_menu;
					}
				}
			}

			$use_shadow = $get_data['use_shadow_menu'];
			if (isset($use_shadow)) {
				$arr_config['use_shadow' . $after_fix] = $use_shadow;
				if ($use_shadow == 'yes') {
					$shadow_color_menu = $get_data['shadow_color_menu'];
					$shadow_type = $get_data['shadow_type_menu'];
					$xposition_menu = $get_data['xposition_menu'];
					$yposition_menu = $get_data['yposition_menu'];
					$shadow_fade_menu = $get_data['shadow_fade_menu'];
					$shadow_spread_menu = $get_data['shadow_spread_menu'];
					if (isset($shadow_color_menu) && isset($shadow_type) && isset($xposition_menu) && isset($yposition_menu) && isset($shadow_fade_menu) && isset($shadow_spread_menu)) {
						$temp = $xposition_menu . 'px ' . $yposition_menu . 'px ' . $shadow_fade_menu . 'px ' . $shadow_spread_menu . 'px ' . $shadow_color_menu . ' ' . ($shadow_type != 'outset' ? $shadow_type : '') . ';';
						$wmbs = '-webkit-box-shadow: ' . $temp . ' -moz-box-shadow: ' . $temp . ' box-shadow: ' . $temp;
						$arr_config['xposition' . $after_fix] = $xposition_menu;
						$arr_config['yposition' . $after_fix] = $yposition_menu;
						$arr_config['shadow_fade' . $after_fix] = $shadow_fade_menu;
						$arr_config['shadow_spread' . $after_fix] = $shadow_spread_menu;
						$arr_config['shadow_color' . $after_fix] = $shadow_color_menu;
						$arr_config['shadow_type' . $after_fix] = $shadow_type;
					}
				}
			}

			$content_css_org .= '.vertical_menu .page-wrapper .page-header, .vertical_menu_df .page-wrapper .page-header, .horizontal_menu .page-wrapper .page-header {
    ' . $b . $bs . $br . $bp . $bw . $bst . $bc . $wmbs . '
  }';

			$content_css_merger = $this->_helper->mergerCssConfigFile('menu', $content_css_org);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_config), 'Netbaseteam_Themeconfig/config');
			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}