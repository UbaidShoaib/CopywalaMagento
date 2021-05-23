<?php

namespace Netbaseteam\Megamenu\Block\Adminhtml\System\Config\Form\Field;

class Colorpicker extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","colorpicker"], function ($) {
                jQuery(document).ready(function () {
                    var $el = jQuery("#' . $element->getHtmlId() . '");
                    $el.css("backgroundColor", "'. $value .'");

                    $el.ColorPicker({
                        color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
					
					$el.bind("input", function() {
						$el.css("background-color", this.value); 
					});
                });
            });
            </script>';
			
		$color = $this->getLayout()->createBlock('Netbaseteam\Megamenu\Block\Navigation')
											->setTemplate('Netbaseteam_Megamenu::colorpicker.phtml')->toHtml(); 
        $html .= $color;
		
		return $html;
    }

}
