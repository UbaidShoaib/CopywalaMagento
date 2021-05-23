<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\General;

use Magento\Framework\View\Result\PageFactory;

class Setting extends \Magento\Framework\App\Action\Action {
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::general_setting')) {
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}
			$arr_config = json_decode($header_config, true);
			$arr_config['cb_use_df_style_button'] = 0;
			$arr_config['cb_use_style_button_login_form'] = 0;

			$get_data = $this->getRequest()->getPost();
			$use_default_style = $get_data['cb_use_df_style_button'];
			$content_css_hover_button = "";
			$content_not_login = '';
			$content_hover_button_login = '';

			$content_css_org = "";

			if (isset($use_default_style) && $use_default_style) {
				$arr_config['cb_use_df_style_button'] = 1;
				$content_css_org = '#html-body .abs-action-primary, #html-body button.primary,
#html-body .page-actions>button.action-primary,
#html-body .page-actions .page-actions-buttons>button.action-primary,
#html-body .page-actions>button.primary,
#html-body .page-actions .page-actions-buttons>button.primary,
#html-body .block-footer .action-add.primary,
#html-body .page-actions>.block-footer .action-add.action-primary,
#html-body .page-actions .page-actions-buttons>.block-footer .action-add.action-primary,
#html-body .page-actions>.block-footer .action-add.primary,
#html-body .page-actions .page-actions-buttons>.block-footer .action-add.primary,
#html-body .page-actions .page-actions-buttons>button.action-secondary,
#html-body .action-primary,
#html-body .action-secondary {
            background-color: #f25322;
            border-color: #f25322;
            color: #ffffff;
            border-width: 1px;
            text-shadow: 1px 1px 0 rgba(0,0,0,0.25);
            border-radius: 5px;
        }
        #html-body button.primary:hover {
        background-color: #db4c16;
    }
    body {
        font-family: "Poppins", sans-serif !important;
        color: #333333 !important;
    }
    .frm-admin-config h2.title {
        color: #41362f;
    }
    nav .level-0._active>a, nav .level-0:hover>a,nav [class*="level-"]:not(.level-0) a:hover {
        color: #f25322;
    }
    .vertical_menu .menu-wrapper .level-0._active>a {
        border-left: 3px solid #f25322;
    }
#html-body.adminhtml-auth-login .action-login.action-primary {
    background-color: #eb5202;
    border-color: #eb5202;
    color: #ffffff;
    text-shadow: 1px 1px 0 rgba(0,0,0,0.25);
}
#html-body.adminhtml-auth-login .action-login.action-primary:hover {
background-color: #ba4000;
border-color: #b84002;
box-shadow: 0 0 0 1px #007bdb;
color: #ffffff;
text-decoration: none;
}
#html-body .data-grid-filters-action-wrap .action-default:hover,
#html-body .admin__data-grid-actions-wrap .admin__action-dropdown:hover {
background-color: #f25322;
border-color: #ee3829;
color: #fff;
}';
			} else {
				$background_color = $get_data['bgcolor_button'];
				$border_color = $get_data['border_color_button'];
				$text_button = $get_data['text_color_button'];
				$radius = $get_data['border_radius_button'];
				$border_wd = $get_data['border_size_button'];
				$bgr_when_hover = $get_data['bgcolor_button_hover'];

				$bgr = "";
				$bder = "";
				$txt = "";
				$rdu = "";
				$bw = "";

				if (isset($background_color) && $background_color != "") {
					$bgr = 'background-color: ' . $background_color . ';';
					$arr_config['button_background_color'] = $background_color;
				}

				if (isset($border_color) && $border_color != "") {
					$bder = 'border-color: ' . $border_color . ';';
					$arr_config['button_border_color'] = $border_color;
				}

				if (isset($text_button) && $text_button != "") {
					$txt = 'color: ' . $text_button . ';';
					$arr_config['button_text_color'] = $text_button;
				}

				if (isset($radius) && $radius >= 0) {
					$rdu = 'border-radius: ' . $radius . 'px;';
					$arr_config['button_border_radius'] = $radius;
				}

				if (isset($border_wd) && $border_wd >= 0) {
					$bw = 'border-width: ' . $border_wd . 'px;';
					$arr_config['button_border_width'] = $border_wd;
				}

				if (isset($bgr_when_hover) && $bgr_when_hover != "") {
					$content_css_hover_button = '
                    #html-body button.scalable.primary:hover,
					#html-body .data-grid-filters-action-wrap .action-default:hover,
					#html-body .admin__data-grid-actions-wrap .admin__action-dropdown:hover,
        .page-actions-buttons button:hover {
            background-color: ' . $bgr_when_hover . ' !important;
            border-color: ' . ($bder != "" ? $border_color : "#ee3829") . ' !important;
            color: #fff !important;
        }
        ';
					$arr_config['button_background_color_hover'] = $bgr_when_hover;
				} else {
					$content_css_hover_button = '';
					$arr_config['button_background_color_hover'] = $content_css_hover_button;
				}

				$content_css_org = '
				#html-body .abs-action-primary, #html-body button.primary,
				#html-body .page-actions>button.action-primary,
				#html-body .page-actions .page-actions-buttons>button.action-primary,
				#html-body .page-actions>button.primary,
				#html-body .page-actions .page-actions-buttons>button.primary,
				#html-body .block-footer .action-add.primary,
				#html-body .page-actions>.block-footer .action-add.action-primary,
				#html-body .page-actions .page-actions-buttons>.block-footer .action-add.action-primary,
				#html-body .page-actions>.block-footer .action-add.primary,
				#html-body .page-actions .page-actions-buttons>.block-footer .action-add.primary,
				#html-body .page-actions .page-actions-buttons>button.action-secondary,
				#html-body .action-primary,
				#html-body .action-secondary {
    ' . $bgr . '
    ' . $bder . '
    ' . $txt . '
    ' . $bw . '
    text-shadow: 1px 1px 0 rgba(0,0,0,0.25);
    ' . $rdu . '
}';

				if (!isset($get_data['cb_use_style_button_login_form'])) {
					$content_not_login = '
				#html-body.adminhtml-auth-login .action-login.action-primary {
    background-color: #eb5202;
    border-color: #eb5202;
    color: #ffffff;
    text-shadow: 1px 1px 0 rgba(0,0,0,0.25);
}';

					$content_hover_button_login = '
				#html-body.adminhtml-auth-login .action-login.action-primary:hover {
background-color: #ba4000;
border-color: #b84002;
box-shadow: 0 0 0 1px #007bdb;
color: #ffffff;
text-decoration: none;
}';
				} else {
					$content_not_login = '
                #html-body.adminhtml-auth-login .action-login.action-primary {
    background-color: ' . $arr_config['button_background_color'] . ';
    border-color: ' . $arr_config['button_border_color'] . ';
    color: ' . $arr_config['button_text_color'] . ';
    text-shadow: 1px 1px 0 rgba(0,0,0,0.25);
}';

					$content_hover_button_login = '
                #html-body.adminhtml-auth-login .action-login.action-primary:hover {
background-color: ' . $arr_config['button_background_color_hover'] . ' !important;
}';

					$arr_config['cb_use_style_button_login_form'] = 1;
				}
			}

			$bgcolor_body = $get_data['bgcolor_body'];
			if (isset($bgcolor_body) && $bgcolor_body != '') {
				$content_css_replace = $this->_helper->replaceBgcolorConfigFile(array('content'), $bgcolor_body);
				$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_replace);
				$arr_config['background_color_content'] = $bgcolor_body;
			}

			$content_css_org .= 'body {';
			$text_color_body = $get_data['text_color_body'];
			if (isset($text_color_body) && $text_color_body != '') {
				$content_css_org .= 'color: ' . $text_color_body . ' !important;';
				$arr_config['text_color_body'] = $text_color_body;
			}
			$base_font = $get_data['base_font'];
			if (isset($base_font) && $base_font != '') {
				$content_css_org .= 'font-family: ' . $this->_helper->getTypeFont($base_font) . ' !important;';
				$arr_config['base_font_body'] = $base_font;
			}
			$base_font_weight = $get_data['base_font_weight'];
			if (isset($base_font_weight) && $base_font_weight != '') {
				$content_css_org .= 'font-weight: ' . $base_font_weight . ' !important;';
				$arr_config['base_font_weight'] = $base_font_weight;
			}
			$base_font_size = $get_data['base_font_size'];
			if (isset($base_font_size) && $base_font_size != '') {
				$content_css_org .= 'font-size: ' . $base_font_size . ' !important;';
				$arr_config['base_font_size'] = $base_font_size;
			}
			$base_line_height = $get_data['base_line_height'];
			if (isset($base_line_height) && $base_line_height != '') {
				$content_css_org .= 'line-height: ' . $base_line_height . ' !important;';
				$arr_config['base_line_height'] = $base_line_height;
			}
			$content_css_org .= '}';

			if (isset($arr_config['base_font_body']) && $arr_config['base_font_body'] != '') {
				$content_css_org .= '#html-body button, #html-body .action-secondary {';
				$content_css_org .= 'font-family: ' . $this->_helper->getTypeFont($base_font) . ' !important; }';
			}

			$content_css_org .= 'h1, h2, h3, h4 {';
			$heading_color_body = $get_data['heading_color_body'];
			if (isset($heading_color_body) && $heading_color_body != '') {
				$content_css_org .= 'color: ' . $heading_color_body . ' !important;';
				$arr_config['heading_color_body'] = $heading_color_body;
			}
			$heading_font = $get_data['heading_font'];
			if (isset($heading_font) && $heading_font != '') {
				$content_css_org .= 'font-family: ' . $this->_helper->getTypeFont($heading_font) . ' !important;';
				$arr_config['heading_font'] = $heading_font;
			}
			$heading_font_weight = $get_data['heading_font_weight'];
			if (isset($heading_font_weight) && $heading_font_weight != '') {
				$content_css_org .= 'font-weight: ' . $heading_font_weight . ' !important;';
				$arr_config['heading_font_weight'] = $heading_font_weight;
			}
			$heading_line_height = $get_data['heading_line_height'];
			if (isset($heading_line_height) && $heading_line_height != '') {
				$content_css_org .= 'line-height: ' . $heading_line_height . ' !important;';
				$arr_config['heading_line_height'] = $heading_line_height;
			}
			$content_css_org .= '}';

			$link_color_body = $get_data['link_color_body'];

			if (isset($link_color_body) && $link_color_body != '') {
				$content_css_org .= 'nav .level-0._active>a, nav .level-0:hover>a, nav [class*="level-"]:not(.level-0) a:hover, nav.admin__menu a.active {
        color: ' . $link_color_body . ' !important;
    }';
				$content_css_org .= '.vertical_menu .menu-wrapper .level-0._active>a {
        border-left: 3px solid ' . $link_color_body . ' !important;
    }';
				$arr_config['link_color_body'] = $link_color_body;
			}

			$content_css_org .= $content_not_login . $content_hover_button_login . $content_css_hover_button;

			$content_css_merger = $this->_helper->mergerCssConfigFile('general', $content_css_org);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);

			$content_css_replace = $content_css_merger;

			$schemer_color = $get_data['schemer_color'];
			if (isset($schemer_color) && $schemer_color != '') {
				$arr_config['schemer_color'] = $schemer_color;

				$schemer_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/color_scheme.json');
				$arr_color = json_decode($schemer_config, true);
				if (count($arr_color) > 0) {
					$bgcolor = explode('|', $arr_color[$schemer_color]);
					if (count($bgcolor) > 0) {
						$content_css_replace = $this->_helper->replaceBgcolorConfigFile(array('footer', 'menu'), $bgcolor[0]);
						$arr_config['background_color_footer'] = $bgcolor[0];
						$arr_config['background_color_menu'] = $bgcolor[0];
					}
				}
			}

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_replace);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_config), 'Netbaseteam_Themeconfig/config');
		}

		$ret = array();
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}