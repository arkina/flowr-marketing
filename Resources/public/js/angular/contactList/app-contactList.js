var contactList = angular.module('contactList', ['ngRoute', 'ui.bootstrap', 'ui.event','toaster']);
contactList.config(function ($routeProvider) {
    var basePath = rootPath + "bundles/flowermarketing/js/angular/contactList/";

    /* app */
    $routeProvider.when('/', {templateUrl: basePath + 'view/editor.html', controller: 'contactListController'});

    $routeProvider.otherwise({redirectTo: '/'});

}).run();
