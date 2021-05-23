require([
    'jquery'
],
    function(jQuery){

    jQuery(document).ready(function(){
        jQuery('li.subcate_list').each(function(){
            var c = jQuery(this).data('toggle');
            jQuery(this).on('click', function(){
                jQuery('li.subcate_list').removeClass('active');
                jQuery('.subcate_content').removeClass('active');
                jQuery(this).addClass('active');
                jQuery('.subcate_content#' + c).addClass('active');
            });
        });
    });

    jQuery(window).load(function(){
        jQuery(".vertical-menu .inner-cms-block").each(function(){
            var block_left = 0;
            var block_cate = 0;
            var block_product = 0;
            var block_right = 0;
            var inner_cms_block = 0;
            block_left = jQuery(this).find(".block-left").innerWidth();
            block_cate = jQuery(this).find(".block-cate").innerWidth();
            block_product = jQuery(this).find(".block-product").innerWidth();
            block_right = jQuery(this).find(".block-right").innerWidth();
            inner_cms_block = block_left + block_cate + block_product + block_right;
            if(jQuery(this).hasClass('nb-repare-width')){
                inner_cms_block += 110;
            }else{
                inner_cms_block += 30;
            }
            if(jQuery(this).hasClass('block-full')){
                inner_cms_block +=25;
            };
            if(!jQuery(this).hasClass('row-static-cate-grid') && !jQuery(this).hasClass('row-product-list')){
                jQuery(this).css('width',inner_cms_block);
            }
        });

        jQuery('.wrap-vertical-menu .btn-menu').on('click',function(){
            if(jQuery('.vertical-menu').hasClass('show')){
                jQuery(".vertical-menu").removeClass('show');
            }else{
                jQuery(".vertical-menu").addClass('show');
            }
            jQuery(".vertical-menu li.menu").each(function(){
                var w_container = jQuery("#header-nav").width();
                var w_parent = jQuery(this).width();
                var w_child = w_container - w_parent;
                jQuery(this).find(".explodedmenu-menu-popup .row-productgrid, .explodedmenu-menu-popup .my-contact-form, .explodedmenu-menu-popup .row-cate-grid,.explodedmenu-menu-popup ._row-product-list, .explodedmenu-menu-popup ._row-static-cate-grid, .explodedmenu-menu-popup .product_list_by_sub_cat").css('width', w_child);
                jQuery(this).find('.product_list_by_sub_cat .inner-cms-block').css('width', '100%');
            });
        });

        jQuery(".horizontal-menu li.menu").each(function(){
            var w_container = jQuery("#header-nav").width();
            var w_parent = jQuery(this).width();
            var w_child = w_container - w_parent;
            jQuery(this).find(".explodedmenu-menu-popup .row-productgrid, .explodedmenu-menu-popup .my-contact-form, .explodedmenu-menu-popup .row-cate-grid,.explodedmenu-menu-popup ._row-product-list, .explodedmenu-menu-popup ._row-static-cate-grid, .explodedmenu-menu-popup .product_list_by_sub_cat").css('width', w_child);
            jQuery(this).find('.product_list_by_sub_cat .inner-cms-block').css('width', '100%');
        });

    });

});