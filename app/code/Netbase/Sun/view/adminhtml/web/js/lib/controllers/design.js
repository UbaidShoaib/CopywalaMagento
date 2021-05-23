var app = angular.module("Nbadmin", ["ngRoute"]);
/* begin directive navconfig */
app.directive("navconfig", function() {
    return {
      	restrict: 'E',
        templateUrl : "views/design/sidebar.html" 
    };
});
app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "views/design/typography.html"
    })
    .when("/link", {
        templateUrl : "views/design/generalcolor.html"
    })
    .when("/button", {
        templateUrl : "views/design/generalcolordark.html"
    })
    .when("/buttoncta", {
        templateUrl : "views/design/generalcolorgray.html"
    })
    .when("/buttonsb", {
        templateUrl : "views/design/generalcomponents.html"
    });
});