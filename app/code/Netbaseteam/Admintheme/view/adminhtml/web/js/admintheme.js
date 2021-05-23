require(['jquery','our_chart'], function($){
	$(document).ready(function(){
		$('.menu-icon').on('click',function(){
			$('.menu-wrapper').toggleClass('active');
		});
	});
});