<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Layout;

use Magento\Framework\View\Result\PageFactory;

class Footer extends \Magento\Framework\App\Action\Action {
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::layout_footer')) {
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}
			$arr_config = json_decode($header_config, true);
			$content_css_org = "";
			$after_fix = "_footer";

			$bgimage_footer = "";
			$b = $bs = $br = $bp = $bw = $bst = $bc = $wmbs = "";

			$get_data = $this->getRequest()->getPost();

			$curr_url_image = $get_data['curr_url_image'];

			$load_bgr_url = $this->_helper->uploadFileWithUser('footer', 'bgimage_footer', $curr_url_image);

			if ($load_bgr_url == '' && (!isset($curr_url_image) || $curr_url_image == '')) {
				$bgcolor_footer = $get_data['bgcolor_footer'];
				if (isset($bgcolor_footer) && $bgcolor_footer != '') {
					$b = 'background: ' . $bgcolor_footer . ';';
					$arr_config['background_color' . $after_fix] = $bgcolor_footer;
				}
			} else {
				$sl_bgsize = $get_data['sl_bgsize_footer'];
				$img_width_footer = $get_data['img_width_footer'];
				$img_height_footer = $get_data['img_height_footer'];
				if (isset($sl_bgsize) && $sl_bgsize != '') {
					$bgsize_footer = '';
					if ($sl_bgsize == 'length') {
						$bgsize_footer = $img_width_footer . 'px ' . $img_height_footer . 'px';
					} elseif ($sl_bgsize == 'percentage') {
						$bgsize_footer = $img_width_footer . '% ' . $img_height_footer . '%';
					} else {
						$bgsize_footer = $sl_bgsize;
					}
					$bs = 'background-size: ' . $bgsize_footer . ';';
					$arr_config['sl_bgsize' . $after_fix] = $sl_bgsize;
					$arr_config['img_width' . $after_fix] = $img_width_footer;
					$arr_config['img_height' . $after_fix] = $img_height_footer;
				}
				$sl_bgrepeat = $get_data['sl_bgrepeat_footer'];
				if (isset($sl_bgrepeat) && $sl_bgrepeat != '') {
					$br = 'background-repeat: ' . $sl_bgrepeat . ';';
					$arr_config['background_repeat' . $after_fix] = $sl_bgrepeat;
				}
				$bg_postype = $get_data['bg_postype_footer'];
				if (isset($bg_postype) && $bg_postype != '') {
					$arr_config['bg_postype' . $after_fix] = $bg_postype;
					if ($bg_postype == 'keyword') {
						$bg_poshkey = $get_data['bg_poshkey_footer'];
						$bg_posvkey = $get_data['bg_posvkey_footer'];
						$bp = 'background-position: ' . $bg_poshkey . ' ' . $bg_posvkey . ';';
						$arr_config['bg_poshkey' . $after_fix] = $bg_poshkey;
						$arr_config['bg_posvkey' . $after_fix] = $bg_posvkey;
					} elseif ($bg_postype == 'percent') {
						$bg_poshper = $get_data['bg_poshper_footer'];
						$bg_posvper = $get_data['bg_posvper_footer'];
						$bp = 'background-position: ' . $bg_poshper . '% ' . $bg_posvper . '%;';
						$arr_config['bg_poshper' . $after_fix] = $bg_poshper;
						$arr_config['bg_posvper' . $after_fix] = $bg_posvper;
					} elseif ($bg_postype == 'pixel') {
						$bg_poshpx = $get_data['bg_poshpx_footer'];
						$bg_posvpx = $get_data['bg_posvpx_footer'];
						$bp = 'background-position: ' . $bg_poshpx . 'px ' . $bg_posvpx . 'px;';
						$arr_config['bg_poshpx' . $after_fix] = $bg_poshpx;
						$arr_config['bg_posvpx' . $after_fix] = $bg_posvpx;
					}
				}
			}

			$use_border = $get_data['use_border_footer'];
			if (isset($use_border)) {
				$arr_config['use_border' . $after_fix] = $use_border;
				if ($use_border == 'yes') {
					$brtop = $get_data['brtop_footer'];
					$brright = $get_data['brright_footer'];
					$brbottom = $get_data['brbottom_footer'];
					$brleft = $get_data['brleft_footer'];
					if (isset($brleft) && isset($brbottom) && isset($brright) && isset($brtop)) {
						$bw = 'border-width: ' . ($brtop != '' ? $brtop : 0) . 'px ' . ($brright != '' ? $brright : 0) . 'px ' . ($brbottom != '' ? $brbottom : 0) . 'px ' . ($brleft != '' ? $brleft : 0) . 'px;';
						$arr_config['brtop' . $after_fix] = $brtop;
						$arr_config['brright' . $after_fix] = $brright;
						$arr_config['brbottom' . $after_fix] = $brbottom;
						$arr_config['brleft' . $after_fix] = $brleft;
					}
					$brtop_style_footer = $get_data['brtop_style_footer'];
					$brright_style_footer = $get_data['brright_style_footer'];
					$brbottom_style_footer = $get_data['brbottom_style_footer'];
					$brleft_style_footer = $get_data['brleft_style_footer'];
					if (isset($brtop_style_footer) && isset($brright_style_footer) && isset($brbottom_style_footer) && isset($brleft_style_footer)) {
						$bst = 'border-style: ' . $brtop_style_footer . ' ' . $brright_style_footer . ' ' . $brbottom_style_footer . ' ' . $brleft_style_footer . ';';
						$arr_config['brtop_style' . $after_fix] = $brtop_style_footer;
						$arr_config['brright_style' . $after_fix] = $brright_style_footer;
						$arr_config['brbottom_style' . $after_fix] = $brbottom_style_footer;
						$arr_config['brleft_style' . $after_fix] = $brleft_style_footer;
					}
					$brtop_color_footer = $get_data['brtop_color_footer'];
					$brright_color_footer = $get_data['brright_color_footer'];
					$brbottom_color_footer = $get_data['brbottom_color_footer'];
					$brleft_color_footer = $get_data['brleft_color_footer'];
					if (isset($brtop_color_footer) && isset($brright_color_footer) && isset($brbottom_color_footer) && isset($brleft_color_footer)) {
						$bc = 'border-color: ' . $brtop_color_footer . ' ' . $brright_color_footer . ' ' . $brbottom_color_footer . ' ' . $brleft_color_footer . ';';
						$arr_config['brtop_color' . $after_fix] = $brtop_color_footer;
						$arr_config['brright_color' . $after_fix] = $brright_color_footer;
						$arr_config['brbottom_color' . $after_fix] = $brbottom_color_footer;
						$arr_config['brleft_color' . $after_fix] = $brleft_color_footer;
					}
				}
			}

			$use_shadow = $get_data['use_shadow_footer'];
			if (isset($use_shadow)) {
				$arr_config['use_shadow' . $after_fix] = $use_shadow;
				if ($use_shadow == 'yes') {
					$shadow_color_footer = $get_data['shadow_color_footer'];
					$shadow_type = $get_data['shadow_type_footer'];
					$xposition_footer = $get_data['xposition_footer'];
					$yposition_footer = $get_data['yposition_footer'];
					$shadow_fade_footer = $get_data['shadow_fade_footer'];
					$shadow_spread_footer = $get_data['shadow_spread_footer'];
					if (isset($shadow_color_footer) && isset($shadow_type) && isset($xposition_footer) && isset($yposition_footer) && isset($shadow_fade_footer) && isset($shadow_spread_footer)) {
						$temp = $xposition_footer . 'px ' . $yposition_footer . 'px ' . $shadow_fade_footer . 'px ' . $shadow_spread_footer . 'px ' . $shadow_color_footer . ' ' . ($shadow_type != 'outset' ? $shadow_type : '') . ';';
						$wmbs = '-webkit-box-shadow: ' . $temp . ' -moz-box-shadow: ' . $temp . ' box-shadow: ' . $temp;
						$arr_config['xposition' . $after_fix] = $xposition_footer;
						$arr_config['yposition' . $after_fix] = $yposition_footer;
						$arr_config['shadow_fade' . $after_fix] = $shadow_fade_footer;
						$arr_config['shadow_spread' . $after_fix] = $shadow_spread_footer;
						$arr_config['shadow_color' . $after_fix] = $shadow_color_footer;
						$arr_config['shadow_type' . $after_fix] = $shadow_type;
					}
				}
			}

			$content_css_org .= 'footer.page-footer {
  ' . $b . $bs . $br . $bp . $bw . $bst . $bc . $wmbs . '
}';

			$content_css_merger = $this->_helper->mergerCssConfigFile('footer', $content_css_org);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_config), 'Netbaseteam_Themeconfig/config');
			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}