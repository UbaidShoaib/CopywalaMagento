define([
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'Magento_Swatches/js/swatch-renderer'
], function ($, _) {
    'use strict';

    $.widget('Netbaseteam.SwatchRenderer', $.mage.SwatchRenderer, {
        /**
         * Render controls
         *
         * @private
         */
        _RenderControls: function () {
            var $widget = this,
                container = this.element,
                classes = this.options.classes,
                chooseText = this.options.jsonConfig.chooseText;

            $widget.optionsMap = {};

            $.each(this.options.jsonConfig.attributes, function () {
                var item = this,
                    options = $widget._RenderSwatchOptions(item),
                    select = $widget._RenderSwatchSelect(item, chooseText),
                    input = $widget._RenderFormInput(item),
                    label = '';

                // Show only swatch controls
                if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                    return;
                }

                if ($widget.options.enableControlLabel) {
                    label +=
                        '<span class="' + classes.attributeLabelClass + '">' + item.label + '</span>' +
                        '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                }

                if ($widget.productForm) {
                    var attributeSelector = '.swatch-attribute ' + '.' + item.code,
                        attributeElm = $widget.productForm.find(attributeSelector);
                    if (attributeElm.length !== 0) {
                        attributeElm.after(input);
                        input = '';
                    }
                }

                // Create new control
                container.append(
                    '<div class="' + classes.attributeClass + ' ' + item.code +
                        '" attribute-code="' + item.code +
                        '" attribute-id="' + item.id + '">' +
                            label +
                        '<div class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                            options + select +
                        '</div>' + input +
                    '</div>'
                );

                $widget.optionsMap[item.id] = {};

                // Aggregate options array to hash (key => value)
                $.each(item.options, function () {
                    if (this.products.length > 0) {
                        $widget.optionsMap[item.id][this.id] = {
                            price: parseInt(
                                $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                                10
                            ),
                            products: this.products
                        };
                    }
                });
            });

            // Connect Tooltip
            container
                .find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]')
                .SwatchRendererTooltip();

            // Hide all elements below more button
            $('.' + classes.moreButton).nextAll().hide();

            // Handle events like click or change
            $widget._EventListener();

            // Rewind options
            $widget._Rewind(container);

            //Emulate click on all swatches from Request
            $widget._EmulateSelected($.parseQuery());
            $widget._EmulateSelected($widget._getSelectedAttributes());
        },
		/**
         * Gets all product media and change current to the needed one
         *
         * @private
         */
        _LoadProductMedia: function () {
			$('.btn-loading-image-wrapper').show();
            var $widget = this,
                $this = $widget.element,
                attributes = {},
                productId = 0,
                mediaCallData,
                mediaCacheKey,

                /**
                 * Processes product media data
                 *
                 * @param {Object} data
                 * @returns void
                 */
                mediaSuccessCallback = function (data) {
                    if (!(mediaCacheKey in $widget.options.mediaCache)) {
                        $widget.options.mediaCache[mediaCacheKey] = data;
                    }
                    $widget._ProductMediaCallback($this, data);
                    $widget._DisableProductMediaLoader($this);
                };

            if (!$widget.options.mediaCallback) {
                return;
            }

            $this.find('[option-selected]').each(function () {
                var $selected = $(this);

                attributes[$selected.attr('attribute-code')] = $selected.attr('option-selected');
            });

			/* old code */
            // if ($('body.catalog-product-view').size() > 0) {
                // Product Page
                // productId = document.getElementsByName('product')[0].value;
            // } else {
                // Category View
                // productId = $this.parents('.product.details.product-item-details')
                    // .find('.price-box.price-final_price').attr('data-product-id');
            // }
			
			/* get product_id from popup option */
			/* productId = document.getElementsByName('product')[0].value; */
			var productId = $("#pid-estimaste").val();
            mediaCallData = {
                'product_id': productId,
                'attributes': attributes,
                'additional': $.parseQuery()
            };
            mediaCacheKey = JSON.stringify(mediaCallData);

            /* if (mediaCacheKey in $widget.options.mediaCache) {
                mediaSuccessCallback($widget.options.mediaCache[mediaCacheKey]);
            } else { */
                mediaCallData.isAjax = true;
                $widget._XhrKiller();
                $widget._EnableProductMediaLoader($this);
                $widget.xhr = $.post(
                    $widget.options.mediaCallback,
                    mediaCallData,
                    mediaSuccessCallback,
                    'json'
                ).done(function (data) {
					$('#estshippingcost_content_option_product img').attr("src", data.large);
                    $('.btn-loading-image-wrapper').hide();
					//$widget._XhrKiller();
                });
            /* } */
        }
    });

    return $.Netbaseteam.SwatchRenderer;
});