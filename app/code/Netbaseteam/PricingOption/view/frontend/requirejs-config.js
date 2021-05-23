var config = {
    paths: {
        "angularjs" : "Netbaseteam_PricingOption/js/libs/angular-1.6.9.min",
        "accounting" : "Netbaseteam_PricingOption/js/libs/accounting.min",
        "serializejson" : "Netbaseteam_PricingOption/js/libs/jquery.serializejson",
        "lodash" : "Netbaseteam_PricingOption/js/libs/lodash",
        "perfect-scrollbar": "Netbaseteam_Onlinedesign/js/assets/js/perfect-scrollbar"
    },
    // map: {
    //     '*': {
    //         'Magento_Swatches/js/swatch-renderer':'Netbaseteam_PricingOption/js/swatch-renderer',
    //         configurable: 'Netbaseteam_PricingOption/js/configurable'
    //     }
    // },
    shim: {
        "lodash" : {
            deps: ['underscore','perfect-scrollbar']
        }
    }
};