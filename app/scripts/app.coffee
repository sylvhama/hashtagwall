'use strict'

angular.module('hashtagwallApp', [
  'ngRoute',
  'ngAnimate'
])
.config ($routeProvider) ->
    $routeProvider
    .when '/',
        templateUrl: 'views/home.html'
        controller: 'HomeCtrl'
    .when '/wall',
        templateUrl: 'views/wall.html'
        controller: 'WallCtrl'
    .when '/404',
        templateUrl: '/404.html'
    .otherwise
        redirectTo: '/404'

.run ['$rootScope', '$route', "$anchorScroll", ($rootScope, $route, $anchorScroll) ->
  $rootScope.$on "$routeChangeSuccess", (event, next, current) ->
    $anchorScroll()
]