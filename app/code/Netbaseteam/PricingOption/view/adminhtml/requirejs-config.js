var config = {
    paths: {
        "admin-options" : "Netbaseteam_PricingOption/js/admin-options",
        "angularjs" : "Netbaseteam_PricingOption/js/libs/angular-1.6.9.min",
        "colorpicker": "Netbaseteam_PricingOption/js/libs/colorpicker",
        "wc-enhanced-select": "Netbaseteam_PricingOption/js/libs/wc-enhanced-select.min"
    },
    shim: {
		"admin-options" : {
			deps: ['jquery','jquery/ui','angularjs', 'colorpicker', 'wc-enhanced-select']
		}
	}
};