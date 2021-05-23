define([
    'jquery',
    'jquery/ui',
    'Netbase_Product/js/lib/pro-tabs'
], function ($) {
    "use strict";
    return function (config) {
        $(document).ready(function(){
            if (config.productTabs.tabAction == 'true') {
                $(config.productTabs.tab).proTabs({
                    tabTitleItem : config.productTabs.eles.tabTitleItem,
                    tabContentItem : config.productTabs.eles.tabContentItem,
                    tabCurrent : config.productTabs.eles.tabCurrent,
                    currentClass : config.productTabs.classActive
                });
            }
        });
    }
});
