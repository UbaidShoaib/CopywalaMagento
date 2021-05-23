define(['jquery','owlCarousel'], function($,owlCarousel){ 
    "use strict";
    $(document).ready(function(){
        jQuery(".post-list .list-content .post-item .post-wrapper .share .share-title,.meta-btn .share-list > span.share-title").click(function(){
            // jQuery(this).next().css("visibility","visible");
            jQuery(this).next().toggleClass("active");
        });
       $('#search_form').submit(function(){
       		var q = $('.search_box').val();
       		if(q.length == 0){
       			return false;
       		}
       }); 
       $('.toolbar-amount').css('display','none');
       addGridStyle(postlistStyle);
       	function addGridStyle(postlistStyle){
       		var postItem = $('.post-list .post-item');       		
       		switch(postlistStyle) {
			    case 'grid-2':
			        $('.post-list .post-item:even').css({
			       		'margin-right':'20px',
			       });
			       $.each(postItem,function(key,item){
			       		$(item).addClass('grid-2');
			       		if(key%2==0){
			       			$(item).addClass('clear');
			       		}
			       });
			       break;
			    case 'grid-3':
			    	var w = $(window).width();
			    	if (480<w && w<640){
			    		$('.post-list .post-item:even').css({
			       			'margin-right':'20px',
			       		});

			       		$.each(postItem,function(key,item){
			       			$(item).addClass('grid-2');
			       			if(key%2==0){
			       				$(item).addClass('clear');
			       			}
			       		});
					}else{
						
				       	$.each(postItem,function(key,item){
				       		$(item).addClass('grid-3');
				       		if(key%3==0){
			       				$(item).addClass('clear');
			       			}	

				       		if (key==1||key==4||key==7) {
				       			$(item).css({
					       			'margin-right':'10px',
					       			'margin-left':'10px'
			       				});
				       		} 
				       		
				       	});
					}
			        
			       break;

			    default:
			        break;
			}
		}

		require(['jquery'], function($) {
	        jQuery(document).ready(function() {		
				$('.owl-carousel').owlCarousel({
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
				            items:4,
				            nav:true,
				            loop:false
				        }
				    }
				})

			});
	    }); 
    });
});