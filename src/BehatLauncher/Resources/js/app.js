var blApp = angular.module('blApp', ['ngRoute', 'ngResource', 'ngSanitize', 'LocalStorageModule', 'pascalprecht.translate']);

blApp.factory('$templateCache', function ($cacheFactory, $http, $injector) {
    var cache = $cacheFactory('templates');
    var allTplPromise;

    return {
        get: function(url) {
            var fromCache = cache.get(url);
            if (fromCache) {
                return fromCache;
            }

            if (!allTplPromise) {
                allTplPromise = $http.get('/template').then(function(response) {
                    for (name in response.data) {
                        cache.put(name, response.data[name]);
                    }

                    return response;
                });
            }

            return allTplPromise.then(function(response) {
                return {
                    status: response.status,
                    data: cache.get(url)
                };
            });
        },

        put: function(key, value) {
            cache.put(key, value);
        }
    };
});

blApp.config(function ($translateProvider, $routeProvider, $locationProvider) {
    $locationProvider.html5Mode(true);

    $translateProvider.useMessageFormatInterpolation();
    $translateProvider.useUrlLoader('/translations');

    var prefix = $("html").attr('ng-path') || '/';

    $routeProvider
        .when('/', {controller: "ProjectListCtrl", templateUrl: "project_list.html"})
        .when('/runs', {controller: "RunListCtrl", templateUrl: "run_list.html"})
        .when('/runs/:id', {controller: "RunShowCtrl", templateUrl: "run_show.html"})
        .when('/create', {controller: "RunCreateCtrl", templateUrl: "run_create.html", reloadOnSearch: false})
        .when('/create-project', {controller: "ProjectCreateCtrl", templateUrl: "project_create.html", reloadOnSearch: false})
        .when('/output/:id', {controller: "OutputFileShowCtrl", templateUrl: "outputFile_show.html"})
    ;
});

blApp.run(function (localStorageService, $translate) {
    var locale = localStorageService.get('locale');
    if (!locale) {
        locale = 'en';
    }
    $translate.use(locale);
});
