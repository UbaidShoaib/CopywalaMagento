<?php
/**
 * @category   Netbaseteam
 * @package    Netbaseteam_Ajaxcart
 * @copyright  Copyright (c) 2018 Netbaseteam
 */

namespace Netbaseteam\Ajaxcart\Block\Config;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","colorpicker"], function ($) {
                $(document).ready(function () {
                    var $el = $("#' . $element->getHtmlId() . '");
                    $el.css("backgroundColor", "'. $value .'");

                    // Attach the color picker
                    /* Color Picker */
                    $($el).each( function() {
                    $(this).minicolors({
                        control: $(this).attr("data-control") || "hue",
                        defaultValue: $(this).attr("data-defaultValue"),
                        format: $(this).attr("data-format") || "hex",
                        keywords: $(this).attr("data-keywords") || "",
                        inline: $(this).attr("data-inline") === "true",
                        letterCase: $(this).attr("data-letterCase") || "lowercase",
                        opacity: $(this).attr("data-opacity"),
                        position: $(this).attr("data-position") || "bottom right",
                        swatches: $(this).attr("data-swatches") ? $(this).attr("data-swatches").split("|") : [],
                        change: function(value, opacity) {
                            $(this).css("backgroundColor", "#" + value);
                            if( !value ) return;
                            if( opacity ) value += ", " + opacity;
                            if( typeof console === "object" ) {
                                console.log(value);
                            }
                          },
                      theme: "bootstrap"
                    });
                });
                /* add selected to select option */
                });
            });
            </script>';
        return $html;
    }

}