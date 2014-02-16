var blApp = angular.module('blApp', ['ngRoute', 'ngResource']);

var urlPrefix = "/app_dev.php/";

blApp.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
    $locationProvider.html5Mode(true);

    $routeProvider
        .when('/', {controller: "ProjectListCtrl", templateUrl: urlPrefix + "templates/project_list.html"})
        .when('/runs', {controller: "RunListCtrl", templateUrl: urlPrefix + "templates/run_list.html"})
        .when('/runs/:id', {controller: "RunShowCtrl", templateUrl: urlPrefix + "templates/run_list.html"})
        .when('/runs/create/:projectName', {controller: "RunCreateCtrl", templateUrl: urlPrefix + "templates/run_create.html"})
        .when('/output/:id', {controller: "OutputFileShowCtrl", templateUrl: urlPrefix + "templates/outputFile_show.html"})
    ;
}]);

blApp.factory('ProjectList', ['$resource', function($resource){
    return $resource('/api/projects', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('RunList', ['$resource', function($resource){
    return $resource('/api/runs', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.controller('ProjectListCtrl', ['$scope', 'ProjectList', function ($scope, ProjectList) {
    $scope.projects = ProjectList.query();
}]);

blApp.controller('RunCreateCtrl', function ($scope) {
});

blApp.controller('RunListCtrl', ['$scope', 'RunList', function ($scope, RunList) {
    $scope.runs = RunList.query();
}]);

blApp.controller('RunShowCtrl', function ($scope) {
});

blApp.controller('OutputFileShowCtrl', function ($scope) {
});
