var templateBuilder = angular.module('templateBuilder', ['ngRoute', 'ui.bootstrap', 'ui.sortable', 'ui.event', 'ngCkeditor', 'ngAnimate','ngDraggable','toaster','flowerTools']);
templateBuilder.config(function ($routeProvider) {
    var basePath = rootPath + "bundles/flowermarketing/js/angular/templateBuilder/";

    /* app */
    $routeProvider.when('/', {templateUrl: basePath + 'view/editor.html', controller: 'templateBuilderController'});

    $routeProvider.otherwise({redirectTo: '/'});

}).run();
