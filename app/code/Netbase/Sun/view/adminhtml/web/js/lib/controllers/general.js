var app = angular.module("Nbadmin", ["ngRoute"]);
/* begin directive navconfig */
app.directive("navconfig", function() {
    return {
      	restrict: 'E',
        templateUrl : "views/genneral/sidebar.html" 
    };
});
app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "views/genneral/generaltypography.html"
    })
    .when("/colors", {
        templateUrl : "views/genneral/generalcolor.html"
    })
    .when("/colorsdark", {
        templateUrl : "views/genneral/generalcolordark.html"
    })
    .when("/colorsgray", {
        templateUrl : "views/genneral/generalcolorgray.html"
    })
    .when("/components", {
        templateUrl : "views/genneral/generalcomponents.html"
    });
});