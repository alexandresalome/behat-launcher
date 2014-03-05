var blApp = angular.module('blApp', ['ngRoute', 'ngResource', 'ngSanitize', 'LocalStorageModule', 'pascalprecht.translate']);

blApp.config(['$translateProvider', function ($translateProvider) {
    $translateProvider.useMessageFormatInterpolation();
    $translateProvider.useUrlLoader('/translations');
}]);

blApp.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
    $locationProvider.html5Mode(true);

    $routeProvider
        .when('/', {controller: "ProjectListCtrl", templateUrl: "/templates/project_list.html"})
        .when('/runs', {controller: "RunListCtrl", templateUrl: "/templates/run_list.html"})
        .when('/runs/:id', {controller: "RunShowCtrl", templateUrl: "/templates/run_show.html"})
        .when('/create', {controller: "RunCreateCtrl", templateUrl: "/templates/run_create.html", reloadOnSearch: false})
        .when('/output/:id', {controller: "OutputFileShowCtrl", templateUrl: "/templates/outputFile_show.html"})
    ;
}]);

blApp.run(['localStorageService', '$translate', function (localStorageService, $translate) {
    var locale = localStorageService.get('locale');
    if (!locale) {
        locale = 'en';
    }
    $translate.use(locale);
}]);
