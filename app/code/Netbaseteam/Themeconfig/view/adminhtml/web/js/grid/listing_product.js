define([
    'Magento_Ui/js/grid/listing'
    ], function (Collection) {
        'use strict';

        return Collection.extend({
            defaults: {
                template: 'Netbaseteam_Themeconfig/ui/grid/listing'
            },
            getRowClass: function (row) {
                // console.log(row);
                if(row.status == 1) {
                    return 'enabled';
                } else {
                    return 'disabled';
                }
            },
            getColorStyle: function(row, col) {
                if(row["status_id"]!=="" && col['index']=='status')
                    return "background-color: "+row["status_id"]+";";
                else
                    return "";
            }
        });
    });