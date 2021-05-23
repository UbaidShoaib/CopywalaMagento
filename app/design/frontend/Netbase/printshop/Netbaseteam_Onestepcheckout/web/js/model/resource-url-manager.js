/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mageUtils'
    ],
    function(customer, urlBuilder, utils) {
        "use strict";
        return {
            getUrlForGiftWrap: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/netbaseteam_onestepcheckout/guest-carts/:cartId/gift-wrap',
                    'customer': '/netbaseteam_onestepcheckout/carts/mine/gift-wrap'
                };

                return this.getUrl(urls, params);
            },

            getUrlForGiftMessage: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/netbaseteam_onestepcheckout/guest-carts/:cartId/gift-message',
                    'customer': '/netbaseteam_onestepcheckout/carts/mine/gift-message'
                };

                return this.getUrl(urls, params);
            },

            getUrlForDelivery: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/netbaseteam_onestepcheckout/guest-carts/:cartId/delivery',
                    'customer': '/netbaseteam_onestepcheckout/carts/mine/delivery'
                };

                return this.getUrl(urls, params);
            },

            getUrlForRemoveItem: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/netbaseteam_onestepcheckout/guest-carts/:cartId/remove-item',
                    'customer': '/netbaseteam_onestepcheckout/carts/mine/remove-item'
                };

                return this.getUrl(urls, params);
            },

            getUrlForUpdateItem: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/netbaseteam_onestepcheckout/guest-carts/:cartId/update-item',
                    'customer': '/netbaseteam_onestepcheckout/carts/mine/update-item'
                };

                return this.getUrl(urls, params);
            },

            getUrlForInitNewsletter: function(quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/netbaseteam_onestepcheckout/guest-carts/:cartId/newsletter',
                    'customer': '/netbaseteam_onestepcheckout/carts/mine/newsletter'
                };

                return this.getUrl(urls, params);
            },

            /** Get url for service */
            getUrl: function(urls, urlParams) {
                var url;

                if (utils.isEmpty(urls)) {
                    return 'Provided service call does not exist.';
                }

                if (!utils.isEmpty(urls['default'])) {
                    url = urls['default'];
                } else {
                    url = urls[this.getCheckoutMethod()];
                }
                return urlBuilder.createUrl(url, urlParams);
            },

            getCheckoutMethod: function() {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            }
        };
    }
);
