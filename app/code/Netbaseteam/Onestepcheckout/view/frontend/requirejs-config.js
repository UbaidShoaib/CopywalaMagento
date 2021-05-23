/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'Netbaseteam_Onestepcheckout/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-address' : {
                'Netbaseteam_Onestepcheckout/js/action/select-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/select-payment-method' : {
                'Netbaseteam_Onestepcheckout/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/action/get-payment-information': {
                'Netbaseteam_Onestepcheckout/js/action/get-payment-information-mixin': true
            },
            'Amazon_Payment/js/action/place-order': {
                'Netbaseteam_Onestepcheckout/js/model/place-order-amazon-mixin': true
            },
            'Magento_Checkout/js/view/payment/list': {
                'Netbaseteam_Onestepcheckout/js/view/payment/list': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Netbaseteam_Onestepcheckout/js/view/summary/abstract-total': true
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Netbaseteam_Onestepcheckout/js/model/step-navigator': true
            },
            'Magento_Paypal/js/action/set-payment-method': {
                'Netbaseteam_Onestepcheckout/js/action/set-payment-method-mixin': true
            },
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'Netbaseteam_Onestepcheckout/js/model/agreements-assigner-mixin': true
            }
        }
    }
};
