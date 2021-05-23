define(
    [
        'jquery',
        'Magento_Ui/js/form/element/single-checkbox',
        'Netbaseteam_Onestepcheckout/js/action/check-gift-wrap'
    ],
    function (
        $,
        Component,
        checkAction
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                listens: {
                    'checked': 'check'
                }
            },

            fee: 0,

            check: function (checked) {
                checkAction(checked);
            }
        });
    }
);
