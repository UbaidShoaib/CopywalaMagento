define(
    [
        'mage/storage',
        'Netbaseteam_Onestepcheckout/js/model/resource-url-manager',
        'jquery',
        'uiComponent',
        'ko',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Netbaseteam_Onestepcheckout/js/action/set-shipping-information',
        'Netbaseteam_Onestepcheckout/js/model/agreement-validator',
        'Netbaseteam_Onestepcheckout/js/model/agreement-validator-old',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Netbaseteam_Onestepcheckout/js/model/amalert',
        'mage/translate'
    ],
    function (
        storage,
        resourceUrlManager,
        $,
        Component,
        ko,
        registry,
        quote,
        setShippingInformationAction,
        checkoutValidator,
        checkoutValidatorOld,
        additionalValidators,
        alert,
        $t
    ) {
        'use strict';

        var paymentsWithRedirect = ['paypal_express', 'paypal_express_bml', 'braintree_paypal', 'braintree'];

        return Component.extend({
            isPlaceOrderActionAllowed: ko.observable(true),

            enableOrderActionAllowed: function () {
                this.isPlaceOrderActionAllowed(true);
            },

            disableOrderActionAllowed: function () {
                this.isPlaceOrderActionAllowed(false);
            },

            requestComponent: function (name) {
                var observable = ko.observable();

                registry.get(name, function (summary) {
                    observable(summary);
                });
                return observable;
            },

            placeOrder: function () {
                var amazonPay = 'amazon_payment';
                var braintreePaypal = 'braintree_paypal';
                var paymentMethod = quote.getPaymentMethod()(),
                    methodCode = paymentMethod ? paymentMethod.method : false;
                var placeOrderText =  $('button.netbaseteam').find('span');
                var textMessage = placeOrderText.text();
                var waitMessage = 'Please Wait';
                placeOrderText.attr("data-bind", "i18n: '" + waitMessage + "'");
                placeOrderText.text(waitMessage);



                if (methodCode == amazonPay) {
                    var billingStreet = quote.billingAddress().street;
                    var shippingStreet = quote.shippingAddress().street;

                    if (!shippingStreet.length
                        || (shippingStreet.length == 1 && !shippingStreet[0].length)) {
                        quote.shippingAddress().street = billingStreet;
                    }
                }

                if (methodCode == braintreePaypal) {
                    this.isPlaceOrderActionAllowed(false);
                    var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
                    if (shippingAddressComponent.validateShippingInformation()) {
                        this.isPlaceOrderActionAllowed(true);
                        var billingStreet = quote.billingAddress().street;
                        if (!billingStreet || (billingStreet.length == 1 && !billingStreet[0].length)) {
                            var shippingAddress = quote.shippingAddress();
                            quote.billingAddress(shippingAddress);
                        }
                    }
                }

                if (!methodCode) {
                    var errorMessage = 'No payment method selected';
                    var shippingAddress = registry.get('checkout.steps.shipping-step.shippingAddress');
                    if (!shippingAddress.validateShippingInformation()) {
                        var errorShippginValidationMessage = shippingAddress.errorValidationMessage();
                        if (errorShippginValidationMessage) {
                            errorMessage += '<br/>';
                            errorMessage += errorShippginValidationMessage;
                        }
                    }
                    alert({content: $t(errorMessage)});
                    return;
                }

                var methodComponent = registry.get('checkout.steps.billing-step.payment.payments-list.' + methodCode);

                if (this.isPlaceOrderActionAllowed() && methodComponent
                    && methodComponent.hasOwnProperty('isReviewRequired')
                    && !methodComponent.isReviewRequired()
                ) {
                    placeOrderText.text(textMessage);
                    $('.payment-method._active .actions-toolbar:not([style*="display: none"]) button[type=submit]').click();
                    return;
                }

                //Netbaseteam_Deliverydate validation
                var netbaseteamDeliveryDate = registry.get('checkout.steps.shipping-step.shippingAddress.shippingAdditional.netbaseteam-delivery-date');
                if (netbaseteamDeliveryDate && netbaseteamDeliveryDate.__proto__.hasOwnProperty('validate')) {
                    if (!netbaseteamDeliveryDate.validate()) {
                        this._focusFirstErrorField();
                        placeOrderText.text(textMessage);
                        return false;
                    }
                }

                //Netbaseteam_Onestepcheckout develivery date validation
                var netbaseteamCheckoutDeliveryDate = registry.get('checkout.steps.shipping-step.amcheckout-delivery-date');
                if (netbaseteamCheckoutDeliveryDate && netbaseteamCheckoutDeliveryDate.__proto__.hasOwnProperty('validate')) {
                    if (!netbaseteamCheckoutDeliveryDate.validate()) {
                        this._focusFirstErrorField();
                        placeOrderText.text(textMessage);
                        return false;
                    }
                }

                additionalValidators.registerValidator(checkoutValidator);
                additionalValidators.registerValidator(checkoutValidatorOld);
                if (!additionalValidators.validate()) {
                    this._focusFirstErrorField();
                    placeOrderText.text(textMessage);
                    return false;
                }

                if (quote.isVirtual()) {
                    this._savePaymentAndPlaceOrder();
                }
                else {
                    var shippingAddress = registry.get('checkout.steps.shipping-step.shippingAddress');
                    if (methodCode == amazonPay || shippingAddress.validateShippingInformation()) {
                        setShippingInformationAction().done(this._savePaymentAndPlaceOrder);
                    } else {
                        var errorMessage = shippingAddress.errorValidationMessage();
                        if (errorMessage) {
                            alert({content: errorMessage});
                            this._focusFirstErrorField();
                        } else {
                            placeOrderText.text(textMessage);
                            this._focusFirstErrorField();
                        }
                    }
                }
            },

            _savePaymentAndPlaceOrder: function () {
                var paymentMethodCode = quote.getPaymentMethod()().method;
                var placeOrderText =  $('button.netbaseteam').find('span');
                var textMessage = $('button.netbaseteam').attr('title');
                if (paymentsWithRedirect.indexOf(paymentMethodCode) !== -1) {
                    var serviceUrl = resourceUrlManager.getUrlForInitNewsletter(quote);
                    var payload    = {
                        cartId: quote.getQuoteId(),
                        email:  quote.guestEmail,
                    };

                    var amcheckoutForm = $('.additional-options input, .additional-options textarea');
                    var amcheckoutFormData = amcheckoutForm.serializeArray();

                    var data = {};
                    var agreements = [];
                    var re = /^agreement\[\d+?\]$/;
                    amcheckoutFormData.forEach(function(item){
                        data[item.name] = item.value;
                        if (re.test(item.name)) {
                            agreements.push(item.value);
                        }
                    });
                    payload.amcheckoutData = data;

                    if (agreements.length && payload.paymentMethod ) {
                        var extensionAttribute = payload.paymentMethod.extension_attributes;
                        if (extensionAttribute.hasOwnProperty('agreement_ids')) {
                            extensionAttribute.agreement_ids = agreements;
                        }
                    }

                    storage.post(serviceUrl, JSON.stringify(payload), false);
                }

                placeOrderText.text(textMessage);
                placeOrderText.attr('data-bind', "i18n: '" + textMessage + "'");
                $('.payment-method._active button[type=submit]').click();
            },

            _focusFirstErrorField: function() {
                var errorField = $('.mage-error:visible:first');
                this.isPlaceOrderActionAllowed(true);
                if (errorField.prop('tagName') && errorField.prop('tagName').toLowerCase() == 'input') {
                    errorField.focus();
                } else if (errorField.prop('tagName') && errorField.prop('tagName').toLowerCase() == 'div') {
                    errorField.prevAll(':input').eq(0).focus();
                }
            }
        });
    }
);
