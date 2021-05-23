define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'Magento_OfflineShipping/js/model/shipping-rates-validator/flatrate',
    'Netbaseteam_Onestepcheckout/js/model/shipping-rates-validation-rules/flatrate'
], function(
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    flatrateShippingRatesValidator,
    netbaseteamFlatrateShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('flatrate', flatrateShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('flatrate', netbaseteamFlatrateShippingRatesValidationRules);

    return Component;
});
