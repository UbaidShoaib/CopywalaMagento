var config = {
    map: {
        '*': {
            swatchRenderer:    	'Netbaseteam_Ajaxcart/js/swatch-renderer',
            swatchRenderer01:    	'Netbaseteam_Ajaxcart/js/swatch-renderer01',
            'cmsmart/customCatalogAddToCart':'Netbaseteam_Ajaxcart/js/customCatalogAddToCart',
            'cmsmart/customCatalogAddToCart01':'Netbaseteam_Ajaxcart/js/customCatalogAddToCart01'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Netbaseteam_Ajaxcart/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};

