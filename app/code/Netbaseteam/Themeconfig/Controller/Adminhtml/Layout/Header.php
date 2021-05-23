<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Layout;

use Magento\Framework\View\Result\PageFactory;

class Header extends \Magento\Framework\App\Action\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	protected $_resultJsonFactory;
	protected $_helper;
	// protected $_authorization;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		PageFactory $resultPageFactory,
		\Magento\Catalog\Model\Session $catalogSession,
		\Netbaseteam\Themeconfig\Helper\Data $helper,
		// \Magento\Backend\App\Action $authorization,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_helper = $helper;
		// $this->_authorization = $authorization;
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::layout_header')) {
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}

			$arr_config = json_decode($header_config, true);
			// $arr_config['cb_use_footer'] = 0;
			// $arr_config['cb_use_content'] = 0;
			$content_css_org = "";
			$after_fix = "_header";

			$bgimage_header = "";
			$b = $bs = $br = $bp = $bw = $bst = $bc = $wmbs = "";

			$get_data = $this->getRequest()->getPost();

			$curr_url_image = $get_data['curr_url_image'];

			$load_bgr_url = $this->_helper->uploadFileWithUser('header', 'bgimage_header', $curr_url_image);

			if ($load_bgr_url == '' && (!isset($curr_url_image) || $curr_url_image == '')) {
				$bgcolor_header = $get_data['bgcolor_header'];
				if (isset($bgcolor_header) && $bgcolor_header != '') {
					$b = 'background: ' . $bgcolor_header . ';';
					$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'background_color', $bgcolor_header);
				}
			} else {
				$sl_bgsize = $get_data['sl_bgsize'];
				$img_width_header = $get_data['img_width_header'];
				$img_height_header = $get_data['img_height_header'];
				if (isset($sl_bgsize) && $sl_bgsize != '') {
					$bgsize_header = '';
					if ($sl_bgsize == 'length') {
						$bgsize_header = $img_width_header . 'px ' . $img_height_header . 'px';
					} elseif ($sl_bgsize == 'percentage') {
						$bgsize_header = $img_width_header . '% ' . $img_height_header . '%';
					} else {
						$bgsize_header = $sl_bgsize;
					}
					$bs = 'background-size: ' . $bgsize_header . ';';
					$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'sl_bgsize', $sl_bgsize);
					$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'img_width', $img_width_header);
					$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'img_height', $img_height_header);
				}
				$sl_bgrepeat = $get_data['sl_bgrepeat'];
				if (isset($sl_bgrepeat) && $sl_bgrepeat != '') {
					$br = 'background-repeat: ' . $sl_bgrepeat . ';';
					$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'background_repeat', $sl_bgrepeat);
				}
				$bg_postype = $get_data['bg_postype'];
				if (isset($bg_postype) && $bg_postype != '') {
					$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_postype', $bg_postype);
					if ($bg_postype == 'keyword') {
						$bg_poshkey = $get_data['bg_poshkey'];
						$bg_posvkey = $get_data['bg_posvkey'];
						$bp = 'background-position: ' . $bg_poshkey . ' ' . $bg_posvkey . ';';
						$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_poshkey', $bg_poshkey);
						$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_posvkey', $bg_posvkey);
					} elseif ($bg_postype == 'percent') {
						$bg_poshper = $get_data['bg_poshper'];
						$bg_posvper = $get_data['bg_posvper'];
						$bp = 'background-position: ' . $bg_poshper . '% ' . $bg_posvper . '%;';
						$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_poshper', $bg_poshper);
						$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_posvper', $bg_posvper);
					} elseif ($bg_postype == 'pixel') {
						$bg_poshpx = $get_data['bg_poshpx'];
						$bg_posvpx = $get_data['bg_posvpx'];
						$bp = 'background-position: ' . $bg_poshpx . 'px ' . $bg_posvpx . 'px;';
						$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_poshpx', $bg_poshpx);
						$arr_config = $this->_helper->addConfigAdmin($get_data, $arr_config, 'bg_posvpx', $bg_posvpx);
					}
				}
			}

			$use_border = $get_data['use_border'];
			if (isset($use_border)) {
				$arr_config['use_border' . $after_fix] = $use_border;
				if ($use_border == 'yes') {
					$brtop = $get_data['brtop'];
					$brright = $get_data['brright'];
					$brbottom = $get_data['brbottom'];
					$brleft = $get_data['brleft'];
					if (isset($brleft) && isset($brbottom) && isset($brright) && isset($brtop)) {
						$bw = 'border-width: ' . ($brtop != '' ? $brtop : 0) . 'px ' . ($brright != '' ? $brright : 0) . 'px ' . ($brbottom != '' ? $brbottom : 0) . 'px ' . ($brleft != '' ? $brleft : 0) . 'px;';

						$arr_config['brtop' . $after_fix] = $brtop;
						$arr_config['brright' . $after_fix] = $brright;
						$arr_config['brbottom' . $after_fix] = $brbottom;
						$arr_config['brleft' . $after_fix] = $brleft;
					}
					$brtop_style_header = $get_data['brtop_style_header'];
					$brright_style_header = $get_data['brright_style_header'];
					$brbottom_style_header = $get_data['brbottom_style_header'];
					$brleft_style_header = $get_data['brleft_style_header'];
					if (isset($brtop_style_header) && isset($brright_style_header) && isset($brbottom_style_header) && isset($brleft_style_header)) {
						$bst = 'border-style: ' . $brtop_style_header . ' ' . $brright_style_header . ' ' . $brbottom_style_header . ' ' . $brleft_style_header . ';';

						$arr_config['brtop_style' . $after_fix] = $brtop_style_header;
						$arr_config['brright_style' . $after_fix] = $brright_style_header;
						$arr_config['brbottom_style' . $after_fix] = $brbottom_style_header;
						$arr_config['brleft_style' . $after_fix] = $brleft_style_header;
					}
					$brtop_color_header = $get_data['brtop_color_header'];
					$brright_color_header = $get_data['brright_color_header'];
					$brbottom_color_header = $get_data['brbottom_color_header'];
					$brleft_color_header = $get_data['brleft_color_header'];
					if (isset($brtop_color_header) && isset($brright_color_header) && isset($brbottom_color_header) && isset($brleft_color_header)) {
						$bc = 'border-color: ' . $brtop_color_header . ' ' . $brright_color_header . ' ' . $brbottom_color_header . ' ' . $brleft_color_header . ';';

						$arr_config['brtop_color' . $after_fix] = $brtop_color_header;
						$arr_config['brright_color' . $after_fix] = $brright_color_header;
						$arr_config['brbottom_color' . $after_fix] = $brbottom_color_header;
						$arr_config['brleft_color' . $after_fix] = $brleft_color_header;
					}
				}
			}

			$use_shadow = $get_data['use_shadow_header'];
			if (isset($use_shadow)) {
				$arr_config['use_shadow' . $after_fix] = $use_shadow;
				if ($use_shadow == 'yes') {
					$shadow_color_header = $get_data['shadow_color_header'];
					$shadow_type = $get_data['shadow_type_header'];
					$xposition_header = $get_data['xposition_header'];
					$yposition_header = $get_data['yposition_header'];
					$shadow_fade_header = $get_data['shadow_fade_header'];
					$shadow_spread_header = $get_data['shadow_spread_header'];
					if (isset($shadow_color_header) && isset($shadow_type) && isset($xposition_header) && isset($yposition_header) && isset($shadow_fade_header) && isset($shadow_spread_header)) {
						$temp = $xposition_header . 'px ' . $yposition_header . 'px ' . $shadow_fade_header . 'px ' . $shadow_spread_header . 'px ' . $shadow_color_header . ' ' . ($shadow_type != 'outset' ? $shadow_type : '') . ';';
						$wmbs = '-webkit-box-shadow: ' . $temp . ' -moz-box-shadow: ' . $temp . ' box-shadow: ' . $temp;

						$arr_config['xposition' . $after_fix] = $xposition_header;
						$arr_config['yposition' . $after_fix] = $yposition_header;
						$arr_config['shadow_fade' . $after_fix] = $shadow_fade_header;
						$arr_config['shadow_spread' . $after_fix] = $shadow_spread_header;
						$arr_config['shadow_color' . $after_fix] = $shadow_color_header;
						$arr_config['shadow_type' . $after_fix] = $shadow_type;
					}
				}
			}

			$content_css_org .= '.vertical_menu .menu-wrapper .logo, .horizontal_menu .menu-wrapper {
    ' . $b . $bs . $br . $bp . $bw . $bst . $bc . $wmbs . '
  }';

			if (isset($get_data['cb_use_footer'])) {
				$test = $this->_helper->copyBGImage(($load_bgr_url != '' ? $load_bgr_url : $curr_url_image), 'header', 'footer');

				$content_css = 'footer.page-footer {
     ' . $b . $bs . $br . $bp . '
   }';

				$content_css_merger2 = $this->_helper->mergerCssConfigFile('footer', $content_css);
				$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger2);

				$arr_config['cb_use_footer'] = 1;
			} else {
				if ($arr_config['cb_use_footer'] == 1) {
					$test = $this->_helper->copyBGImage('', 'header', 'footer');

					$bgcolor_footer = '#f25322';

					$content_css_replace = $this->_helper->replaceBgcolorConfigFile(array('footer'), $bgcolor_footer);

					$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_replace);

					$arr_config['background_color_footer'] = $bgcolor_footer;
					$arr_config['cb_use_footer'] = 0;
				}
			}

			if (isset($get_data['cb_use_content'])) {
				$test = $this->_helper->copyBGImage(($load_bgr_url != '' ? $load_bgr_url : $curr_url_image), 'header', 'content');

				$content_css = '.page-wrapper .notices-wrapper, .page-wrapper .page-content {
     background: transparent;
   }';
				$content_css .= '.page-wrapper {
     ' . $b . $bs . $br . $bp . '
   }';

				$content_css_merger2 = $this->_helper->mergerCssConfigFile('content', $content_css);
				$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger2);

				$arr_config['cb_use_content'] = 1;
			} else {
				if ($arr_config['cb_use_content'] == 1) {
					$test = $this->_helper->copyBGImage('', 'header', 'content');

					$bgcolor_body = '#f5f5f5';

					$content_css_replace = $this->_helper->replaceBgcolorConfigFile(array('content'), $bgcolor_body);

					$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_replace);

					$arr_config['background_color_content'] = $bgcolor_body;
					$arr_config['cb_use_content'] = 0;
				}
			}

			$content_css_merger = $this->_helper->mergerCssConfigFile('header', $content_css_org);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_config), 'Netbaseteam_Themeconfig/config');

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}

// protected function _isAllowed()
	// {
	//   return $this->_authorization->isAllowed('Netbaseteam_Themeconfig::layout_header');
	// }

}