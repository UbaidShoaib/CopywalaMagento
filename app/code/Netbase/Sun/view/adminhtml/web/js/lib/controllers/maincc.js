
	var app = angular.module('myApp', []);
	app.controller("myApp", function($scope) {

	});
	app.directive("navconfig", function() {
	    return {
	    	restrict: 'E',
	        templateUrl: 'Netbase_Sun::sidebar.html',
	        replace: true,
	    };
	});  
	 angular.bootstrap(document, ['myApp']);	
