var blApp = angular.module('blApp', ['ngRoute', 'ngResource']);

blApp.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
    $locationProvider.html5Mode(true);

    $routeProvider
        .when('/', {controller: "ProjectListCtrl", templateUrl: "/templates/project_list.html"})
        .when('/runs', {controller: "RunListCtrl", templateUrl: "/templates/run_list.html"})
        .when('/runs/:id', {controller: "RunShowCtrl", templateUrl: "/templates/run_show.html"})
        .when('/runs/create/:projectName', {controller: "RunCreateCtrl", templateUrl: "/templates/run_create.html"})
        .when('/output/:id', {controller: "OutputFileShowCtrl", templateUrl: "/templates/outputFile_show.html"})
    ;
}]);

blApp.factory('ProjectList', ['$resource', function ($resource){
    return $resource('/project_list.json', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('Project', ['$resource', function ($resource){
    return $resource('/project/:name.json', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('RunList', ['$resource', function ($resource){
    return $resource('/run_list.json', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('Run', ['$resource', function ($resource){
    return $resource('/runs/:id.json', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.controller('ProjectListCtrl', ['$scope', 'ProjectList', function ($scope, ProjectList) {
    $scope.projects = ProjectList.query();
}]);

blApp.controller('RunListCtrl', ['$scope', 'RunList', function ($scope, RunList) {
    $scope.runs = RunList.query();
}]);

blApp.controller('RunShowCtrl', ['$scope', '$routeParams', 'Run', 'Project', function ($scope, $routeParams, Run) {
    $scope.run = Run.get({id: $routeParams.id});
}]);

blApp.controller('RunCreateCtrl', ['$scope', '$routeParams', 'Project', function ($scope, $routeParams, Project) {
    $scope.project = Project.get({name: $routeParams.projectName});
}]);

blApp.controller('OutputFileShowCtrl', function ($scope) {
});
