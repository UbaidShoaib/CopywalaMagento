require([
        'jquery',
        'uiComponent',
        'Magento_Customer/js/customer-data',
        'ko',
        'underscore',
        'sidebar',
        'Magento_Catalog/product/view/validation'
    ],
    function ($) {
        "use strict";

        $(document).ready(function ($) {
            var please_wait = $('#nb-ajax-cart-loading');
            var popup_success = $('#nb_popupmess');
            popup_success.hide();
            var minicartSelector = '[data-block="minicart"]';
            $('#ajaxcart_content_option_product').hide();

            /* click add to cart in general page or parent category */

            $('body').on('click', ".product-item-info button[data-post]", function (event) {
                $(minicartSelector).trigger('contentLoading');
                event.stopPropagation();
                please_wait.show();
                $('#nb_shadow').show();
                var data_post = $(this).attr("data-post");
                var data_action = $.parseJSON(data_post);
                var form_key = $("input[name='form_key']").attr("value");
                if (data_post != null) {
                    var data_post_arr = JSON.parse(data_post);
                    // var pid = data_post_arr["data"]["product"];
                    var url = data_action['action'] + "ajax/1/utype/general-add";
                    var data = {
                        'form_key': form_key
                    };
                    if (data_action['action'].indexOf('checkout/cart/add') == -1) {
                        url = baseUrl + 'ajaxcart/wishlist/cart/isAjax/1/';
                        data = data_action['data'];
                        data.form_key = form_key;
                    }
                    /*fly image Effect*/
                    var imgtodrag = $(this).parents('.product-item').find('.product-image-photo');
                    $.ajax({
                        url: url,
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        success: function (res) {
                            please_wait.hide();
                            if (res["error"] == 0) {
                                popup_success.html(res["html_popup"]);
                                popup_success.show();
                                $('#nb_shadow').show();

                                if (res['item']) {
                                    $('#item_' + res["item"]).remove();
                                }
                                return false;
                            } else if (res["popup_option"] == 1) {
                                $('#ajaxcart_content_option_product .option_content').html(res["html_popup_option"]);
                                $('#ajaxcart_content_option_product').show();
                                $('#nb_shadow').show();
                                if (imgtodrag && showProductImage != 0) {
                                    var imagUrl = imgtodrag.attr('src');
                                    if (imgtodrag.attr('src').indexOf('thumbnail/75x90') != -1) {
                                        imagUrl = imagUrl.replace("thumbnail/75x90", "small_image/240x300");
                                    }
                                    var imageRepalce = '<img src="' + imagUrl + '" class="photo image"/>';
                                    $("#ajaxcart_content_option_product .photo").replaceWith(imageRepalce);
                                }
                                /*if add product in wishlist page*/
                                if (res['item']) {
                                    $('#ajaxcart_content_option_product').prepend('<span class="nb_w_item">' + res['item'] + '</span>');
                                }
                            } else {
                                $('#nb_shadow').hide();
                            }
                        }
                    });
                }
            });

            /* for add to cart in sub-category (has swatch color option) page and comparse page */
            $('form[data-role="tocart-form"]').on('submit', function (e) {
                e.stopImmediatePropagation();
                please_wait.show();
                $('#nb_shadow').show();
                // e.stopImmediatePropagation();
                var cartForm = $(this);
                $(minicartSelector).trigger('contentLoading');
                var _addToCartButton = cartForm.find("#product-addtocart-button");
                var _addToCartButtonDisabledClass = 'disabled';

                var url = cartForm.attr('action') + "ajax/1/utype/view-option";
                var imgtodrag = $(this).parents('.product-item').find('.product-image-photo');
                /*fly image Effect*/
                $.ajax({
                    url: url,
                    data: cartForm.serialize(),
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function () {
                    },
                    success: function (res) {
                        please_wait.hide();
                        if (res["error"] == 0) {
                            popup_success.html(res["html_popup"]);
                            popup_success.show();
                            $('#nb_shadow').show();
                            if (imgtodrag && showProductImage != 0) {
                                var imageRepalce = '<img src="' + imgtodrag.attr('src') + '" class="photo image"/>';
                                $("#nb_popupmess .photo").replaceWith(imageRepalce);
                            }
                            _addToCartButton.removeClass(_addToCartButtonDisabledClass);
                            please_wait.hide();
                            return false;
                        } else if (res['popup_option'] == 1) {
                            $('#ajaxcart_content_option_product .option_content').html(res["html_popup_option"]);
                            $('#ajaxcart_content_option_product').show();
                            $('#nb_shadow').show();
                        } else {
                            $('#nb_shadow').hide();
                        }
                        $('#nb_shadow').hide();
                    },
                    error: function (response) {
                        please_wait.hide();
                    }
                });
                return false;
            });

            /*add cart in product detail page*/
            $('.ajax-popup-to-cart').closest('#product_addtocart_form').mage('validation');

            var please_wait = $('#nb-ajax-cart-loading');
            var popup_success = $('#nb_popupmess');
            popup_success.hide();

            $('#product_addtocart_form').on('submit', function(e) {
                var flag = true;
                var is_require = $('.check-require').attr('is-require');
                if(is_require == 1) {
                    var validateUploadNb = $('#upload-file-nb').length;
                    var validateUploadFile = $('.ajax-file-upload-container .ajax-file-upload-statusbar .ajax-file-upload-filename').length;
                    if (validateUploadFile == 0 && validateUploadNb == 1) {
                        $(".nb_orderupload #label_oderupload div.error").html("<div generated='true' class='button-append-required mage-error'>Please add photo to your cart.</div>");
                        $(".nb_orderupload .fileuploader-box").addClass("error-upload-button");
                        $("#label_oderupload").attr('id', 'buttonJump');
                        location.href = "#buttonJump";
                        flag = false;
                    }
                }
                var isValid = $(this).validation('isValid');
                if(isValid && flag){
                    e.stopImmediatePropagation();
					please_wait.show();
					$('#nb_shadow').show();
                    var cartForm = $(this);
                    var url =cartForm.attr('action')+"ajax/1/utype/detail-add";
                    var currentUrl = window.location.href;
                    var form_key = $("input[name='form_key']").attr("value");
                    if(currentUrl.indexOf('wishlist/index/index/') != -1){
                        var item = $('span.nb_w_item').text();
                        url = url+'/item/'+item;
                    }
                    $.ajax({
                        url: url,
                        data: cartForm.serialize(),
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function() {
                        },
                        success: function(res) {
                            if(res["error"]==0){
                                please_wait.hide();
                                popup_success.html(res["html_popup"]);
                                $('#ajaxcart_content_option_product').hide();
                                popup_success.show();
                                if(res['item']){
                                    $('#item_'+res["item"]).remove();
                                }
                                return false;
                            }
                        },
                        error: function (response) {
                            please_wait.hide();
                        }
                    });
                    e.preventDefault();
                    return false;
                }
            });

            // /* click update all qty */
            // $(".cart-container .update").click(function (event) {
            //     event.preventDefault();
            //     // please_wait.show();
            //     $(this).text("Updating Shopping Cart");
            //     var item_id, urlEditArray, qty;
            //     var item_ids = [];
            //     var item_qtys = [];
            //     $("#shopping-cart-table a").each(function (e) {
            //         urlEditArray = this.href.split("/");
            //         for (var i = 0; i < urlEditArray.length; i++) {
            //             if (urlEditArray[i] == "id") {
            //                 item_id = urlEditArray[i + 1];
            //                 item_ids.push(item_id);
            //                 qty = $('#cart-' + item_id + '-qty').val();
            //                 item_qtys.push(qty);
            //                 break;
            //             }
            //         }
            //     });
            //
            //     /* update qty */
            //     $.ajax({
            //         url: updateQtyUrl,
            //         data: {
            //             item_id: item_ids.join(","),
            //             item_qty: item_qtys.join(","),
            //             utype: "multi-update-qty"
            //         },
            //         type: 'post',
            //         dataType: 'json',
            //
            //         success: function (res) {
            //             if (!res["error"]) {
            //                 $.ajax({
            //                     url: updateQtyUrl,
            //                     data: {
            //                         item_id: item_ids.join(","),
            //                         item_qty: item_qtys.join(","),
            //                         utype: "multi-update-qty"
            //                     },
            //                     type: 'post',
            //                     dataType: 'json',
            //                     success: function (res) {
            //                         please_wait.hide();
            //                         if (!res["error"]) {
            //                             $("#cart-totals tbody").html(res["sub_total_html"]);
            //                             $("#cart-totals tbody").append(res["grand_total_html"]);
            //                             $(".cart-container .action.update").text("Update Shopping Cart");
            //                         }
            //                     }
            //                 });
            //             } else {
            //                 please_wait.hide();
            //                 alert(res["error_msg"]);
            //             }
            //         },
            //         error: function (response) {
            //             please_wait.hide();
            //         }
            //     });
            // });

            /* change qty input */
            $('#shopping-cart-table').on('keyup', '.input-text.qty', function (event) {
                /* get Price column in shopping cart */
                var qty = $(this).val();
                if (qty != 0 || qty != "") {
                    var strPrice = $(event.target).parents(':eq(4)').find('.price .cart-price .price').text();
                    var currentProductPrice = strPrice.replace(/[$,]+/g, "");
                    var priceFloat = parseFloat(currentProductPrice * qty);
                    var priceChanged = currencyCode + parseFloat(priceFloat, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
                    /* set pice after update qty */
                    $(event.target).parents(':eq(4)').find('.subtotal .cart-price .price').text(priceChanged);
                }
            });

            /* fly product image to cart */
            function flyImageToCart(imgtodrag) {
                if (getImageFlyEffect != 0) {
                    var imgtodragPosition = imgtodrag.offset();
                    var cartPosition = $('[data-block="minicart"]').offset();
                    if (imgtodrag) {
                        var imgclone = imgtodrag.clone();
                        imgclone.removeClass('product-image-photo');
                        $('body').append(imgclone);
                        imgclone.css({
                            'opacity': '0.8',
                            'position': 'absolute',
                            'height': imgtodrag.height,
                            'width': imgtodrag.width,
                            'z-index': '10000',
                            'top': imgtodragPosition.top + 'px',
                            'left': imgtodragPosition.left + 'px'
                        }).animate({
                            'top': cartPosition.top + 'px',
                            'left': cartPosition.left + 'px',
                            'width': '40px',
                            'height': '40px'
                        }, 1500, function () {
                            $(this).detach();
                        });
                    }
                }
            }
        });
    });