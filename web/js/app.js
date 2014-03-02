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
    return $resource('/projects', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('Project', ['$resource', function ($resource){
    return $resource('/projects/:name', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('RunList', ['$resource', function ($resource){
    return $resource('/runs', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('Run', ['$resource', function ($resource){
    return $resource('/runs/:id', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.controller('ProjectListCtrl', ['$scope', '$interval', 'ProjectList', function ($scope, $interval, ProjectList) {
    $scope.projects = ProjectList.query();

    var refresh = $interval(function() {
        ProjectList.query().$promise.then(function (projects) {
            $scope.projects = projects;
        });
    }, 2000);

    $scope.$on('$destroy', function() {
        $interval.cancel(refresh);
    });
}]);

blApp.controller('RunListCtrl', ['$scope', '$interval', 'RunList', function ($scope, $interval, RunList) {
    $scope.runs = RunList.query();

    var refresh = $interval(function() {
        RunList.query().$promise.then(function (runs) {
            $scope.runs = runs;
        });
    }, 2000);

    $scope.$on('$destroy', function() {
        $interval.cancel(refresh);
    });
}]);

blApp.controller('RunShowCtrl', ['$scope', '$routeParams', '$http', '$location', '$interval', 'Run', 'Project', function ($scope, $routeParams, $http, $location, $interval, Run, Project) {
    $scope.run = Run.get({id: $routeParams.id});

    var refresh = $interval(function() {
        Run.get({id: $routeParams.id}).$promise.then(function (run) {
            $scope.run = run;
        });
    }, 2000);

    $scope.$on('$destroy', function() {
        $interval.cancel(refresh);
    });

    $scope.restartAll = function () {
        $http({method: 'POST', url: '/runs/' + $scope.run.id + '/restart'});
    };

    $scope.restartFailed = function () {
        $http({method: 'POST', url: '/runs/' + $scope.run.id + '/restart?failed=1'});
    };

    $scope.restartUnit = function (id) {
        $http({method: 'POST', url: '/runs/' + $scope.run.id + '/restart?unit=' + id});
    };

    $scope.stop = function (id) {
        $http({method: 'POST', url: '/runs/' + $scope.run.id + '/stop'});
    };

    $scope.delete = function (id) {
        $http({method: 'POST', url: '/runs/' + $scope.run.id + '/delete'})
            .success(function () {
                $location.path('/');
            })
        ;
    };
}]);

blApp.controller('RunCreateCtrl', ['$scope', '$routeParams', '$location', 'Run', 'Project', function ($scope, $routeParams, $location, Run, Project) {
    $scope.project = Project.get({name: $routeParams.projectName});

    $scope.run = {
        title: null,
        projectName: $routeParams.projectName,
        properties: {},
        features: {}
    };

    $scope.submit = function () {
        // convert array of booleans to array of strings
        var features = $scope.run.features;
        $scope.run.features = [];
        for (i in features) {
            $scope.run.features.push(i);
        }

        var run = new Run($scope.run);
        run.$save(function (msg, headers) {
            $location.path('/runs/' + msg.id);
        });
    };
}]);

blApp.directive('decorateFeatureDirectory', function () {
    return {
        link : function(scope, element, attrs) {
            decorateFeatureDirectory(element);
        }
    };
});

blApp.controller('OutputFileShowCtrl', function ($scope) {
});
