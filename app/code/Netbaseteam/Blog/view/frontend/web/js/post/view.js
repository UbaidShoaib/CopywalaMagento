define(['jquery','owlCarousel'], function($,owlCarousel){ 
"use strict";
    $(document).ready(function(){
        $( '<em> *</em>').insertAfter("#form-content .captcha .label span"); 
        jQuery('.realted-content .owl-carousel').owlCarousel({
            loop:true,
            margin:30,
            responsiveClass:true,
            navText: ["<i class='icon-left-open'></i>", "<i class='icon-right-open'></i>"],
            navClass: ['owl-prev', 'owl-next'],
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:3,
                    nav:true,
                    loop:false
                }
            }
                
        });  
    }); 
});