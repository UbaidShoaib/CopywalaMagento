<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Layout;

use Magento\Framework\View\Result\PageFactory;

class Content extends \Magento\Framework\App\Action\Action {
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
		if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::layout_content')) {
			$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/custom.json');
			if (!$this->_helper->isJson($header_config)) {
				$header_config = $this->_helper->_readFile('Netbaseteam_Themeconfig/config/default.json');
			}
			$arr_config = json_decode($header_config, true);
			$content_css_org = "";
			$after_fix = "_content";

			$bgimage_content = "";
			$b = $bs = $br = $bp = $bw = $bst = $bc = $wmbs = "";

			$get_data = $this->getRequest()->getPost();

			$curr_url_image = $get_data['curr_url_image'];

			$load_bgr_url = $this->_helper->uploadFileWithUser('content', 'bgimage_content', $curr_url_image);

			if ($load_bgr_url == '' && (!isset($curr_url_image) || $curr_url_image == '')) {
				$bgcolor_content = $get_data['bgcolor_content'];
				if (isset($bgcolor_content) && $bgcolor_content != '') {
					$b = 'background: ' . $bgcolor_content . ';';
					$arr_config['background_color' . $after_fix] = $bgcolor_content;
				}
			} else {
				$sl_bgsize = $get_data['sl_bgsize_content'];
				$img_width_content = $get_data['img_width_content'];
				$img_height_content = $get_data['img_height_content'];
				if (isset($sl_bgsize) && $sl_bgsize != '') {
					$bgsize_content = '';
					if ($sl_bgsize == 'length') {
						$bgsize_content = $img_width_content . 'px ' . $img_height_content . 'px';
					} elseif ($sl_bgsize == 'percentage') {
						$bgsize_content = $img_width_content . '% ' . $img_height_content . '%';
					} else {
						$bgsize_content = $sl_bgsize;
					}
					$bs = 'background-size: ' . $bgsize_content . ';';
					$arr_config['sl_bgsize' . $after_fix] = $sl_bgsize;
					$arr_config['img_width' . $after_fix] = $img_width_content;
					$arr_config['img_height' . $after_fix] = $img_height_content;
				}
				$sl_bgrepeat = $get_data['sl_bgrepeat_content'];
				if (isset($sl_bgrepeat) && $sl_bgrepeat != '') {
					$br = 'background-repeat: ' . $sl_bgrepeat . ';';
					$arr_config['background_repeat' . $after_fix] = $sl_bgrepeat;
				}
				$bg_postype = $get_data['bg_postype_content'];
				if (isset($bg_postype) && $bg_postype != '') {
					$arr_config['bg_postype' . $after_fix] = $bg_postype;
					if ($bg_postype == 'keyword') {
						$bg_poshkey = $get_data['bg_poshkey_content'];
						$bg_posvkey = $get_data['bg_posvkey_content'];
						$bp = 'background-position: ' . $bg_poshkey . ' ' . $bg_posvkey . ';';
						$arr_config['bg_poshkey' . $after_fix] = $bg_poshkey;
						$arr_config['bg_posvkey' . $after_fix] = $bg_posvkey;
					} elseif ($bg_postype == 'percent') {
						$bg_poshper = $get_data['bg_poshper_content'];
						$bg_posvper = $get_data['bg_posvper_content'];
						$bp = 'background-position: ' . $bg_poshper . '% ' . $bg_posvper . '%;';
						$arr_config['bg_poshper' . $after_fix] = $bg_poshper;
						$arr_config['bg_posvper' . $after_fix] = $bg_posvper;
					} elseif ($bg_postype == 'pixel') {
						$bg_poshpx = $get_data['bg_poshpx_content'];
						$bg_posvpx = $get_data['bg_posvpx_content'];
						$bp = 'background-position: ' . $bg_poshpx . 'px ' . $bg_posvpx . 'px;';
						$arr_config['bg_poshpx' . $after_fix] = $bg_poshpx;
						$arr_config['bg_posvpx' . $after_fix] = $bg_posvpx;
					}
				}
			}

			$use_border = $get_data['use_border_content'];
			if (isset($use_border)) {
				$arr_config['use_border' . $after_fix] = $use_border;
				if ($use_border == 'yes') {
					$brtop = $get_data['brtop_content'];
					$brright = $get_data['brright_content'];
					$brbottom = $get_data['brbottom_content'];
					$brleft = $get_data['brleft_content'];
					if (isset($brleft) && isset($brbottom) && isset($brright) && isset($brtop)) {
						$bw = 'border-width: ' . ($brtop != '' ? $brtop : 0) . 'px ' . ($brright != '' ? $brright : 0) . 'px ' . ($brbottom != '' ? $brbottom : 0) . 'px ' . ($brleft != '' ? $brleft : 0) . 'px;';
						$arr_config['brtop' . $after_fix] = $brtop;
						$arr_config['brright' . $after_fix] = $brright;
						$arr_config['brbottom' . $after_fix] = $brbottom;
						$arr_config['brleft' . $after_fix] = $brleft;
					}
					$brtop_style_content = $get_data['brtop_style_content'];
					$brright_style_content = $get_data['brright_style_content'];
					$brbottom_style_content = $get_data['brbottom_style_content'];
					$brleft_style_content = $get_data['brleft_style_content'];
					if (isset($brtop_style_content) && isset($brright_style_content) && isset($brbottom_style_content) && isset($brleft_style_content)) {
						$bst = 'border-style: ' . $brtop_style_content . ' ' . $brright_style_content . ' ' . $brbottom_style_content . ' ' . $brleft_style_content . ';';
						$arr_config['brtop_style' . $after_fix] = $brtop_style_content;
						$arr_config['brright_style' . $after_fix] = $brright_style_content;
						$arr_config['brbottom_style' . $after_fix] = $brbottom_style_content;
						$arr_config['brleft_style' . $after_fix] = $brleft_style_content;
					}
					$brtop_color_content = $get_data['brtop_color_content'];
					$brright_color_content = $get_data['brright_color_content'];
					$brbottom_color_content = $get_data['brbottom_color_content'];
					$brleft_color_content = $get_data['brleft_color_content'];
					if (isset($brtop_color_content) && isset($brright_color_content) && isset($brbottom_color_content) && isset($brleft_color_content)) {
						$bc = 'border-color: ' . $brtop_color_content . ' ' . $brright_color_content . ' ' . $brbottom_color_content . ' ' . $brleft_color_content . ';';
						$arr_config['brtop_color' . $after_fix] = $brtop_color_content;
						$arr_config['brright_color' . $after_fix] = $brright_color_content;
						$arr_config['brbottom_color' . $after_fix] = $brbottom_color_content;
						$arr_config['brleft_color' . $after_fix] = $brleft_color_content;
					}
				}
			}

			$use_shadow = $get_data['use_shadow_content'];
			if (isset($use_shadow)) {
				$arr_config['use_shadow' . $after_fix] = $use_shadow;
				if ($use_shadow == 'yes') {
					$shadow_color_content = $get_data['shadow_color_content'];
					$shadow_type = $get_data['shadow_type_content'];
					$xposition_content = $get_data['xposition_content'];
					$yposition_content = $get_data['yposition_content'];
					$shadow_fade_content = $get_data['shadow_fade_content'];
					$shadow_spread_content = $get_data['shadow_spread_content'];
					if (isset($shadow_color_content) && isset($shadow_type) && isset($xposition_content) && isset($yposition_content) && isset($shadow_fade_content) && isset($shadow_spread_content)) {
						$temp = $xposition_content . 'px ' . $yposition_content . 'px ' . $shadow_fade_content . 'px ' . $shadow_spread_content . 'px ' . $shadow_color_content . ' ' . ($shadow_type != 'outset' ? $shadow_type : '') . ';';
						$wmbs = '-webkit-box-shadow: ' . $temp . ' -moz-box-shadow: ' . $temp . ' box-shadow: ' . $temp;
						$arr_config['xposition' . $after_fix] = $xposition_content;
						$arr_config['yposition' . $after_fix] = $yposition_content;
						$arr_config['shadow_fade' . $after_fix] = $shadow_fade_content;
						$arr_config['shadow_spread' . $after_fix] = $shadow_spread_content;
						$arr_config['shadow_color' . $after_fix] = $shadow_color_content;
						$arr_config['shadow_type' . $after_fix] = $shadow_type;
					}
				}
			}

			$content_css_org .= '.page-wrapper .notices-wrapper, .page-wrapper .page-content {
  background: transparent;
}';
			$content_css_org .= '.page-wrapper {
  ' . $b . $bs . $br . $bp . $bw . $bst . $bc . $wmbs . '
}';

			$content_css_merger = $this->_helper->mergerCssConfigFile('content', $content_css_org);

			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/config/custom.json', json_encode($arr_config), 'Netbaseteam_Themeconfig/config');
			$this->_helper->_writeContent2File('Netbaseteam_Themeconfig/css/style_config.min.css', $content_css_merger);
		}
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}