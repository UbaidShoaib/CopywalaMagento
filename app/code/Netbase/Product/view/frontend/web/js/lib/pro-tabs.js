define([
    'jquery',
    'jquery/ui'
], function ($) {
    "use strict";
    $.widget('netbase.proTabs', {
        options: {
           tabTitleItem : ".product-list .tab-link",
           tabContentItem : ".product-list .nb-list",
           tabCurrent : '.current',
           currentClass: 'active'
        },
        _create: function () {
            let self = this;
            if (!self.options.disabled) {
                self._loadTabs();
                self._actionTab();
            }
        },
        _loadTabs: function () {
            let self = this;
            let curentTab = $(self.options.tabCurrent);
            if (curentTab.length > 0) {
                let tab = curentTab.data('rev');
                self._changeCurrentTabs(tab);
            }
        },
        _changeCurrentTabs: function (tab) {
            let self = this;
            self._resetTab();
            let tabList = $(self.options.tabContentItem);
            $.each(tabList, function(k,item){
                if ($(item).data('rev') == tab) {
                    $(item).addClass(self.options.currentClass);
                    return false;
                }
            });
        },
        _resetTab :function () {
            let self = this;
            let tabList = $(self.options.tabContentItem);
            tabList.removeClass(self.options.currentClass);
        },
        _actionTab : function(){
            let self = this;
            $(self.options.tabTitleItem).on('click', function(){
                let tab = $(this).data('rev');
                if(tab){
                    self._changeCurrentTabs(tab);
                    $(self.options.tabTitleItem).removeClass(self.options.currentClass);
                    $(this).addClass(self.options.currentClass);
                }
                
            });
        }

    });

    return $.netbase.proTabs;
});
