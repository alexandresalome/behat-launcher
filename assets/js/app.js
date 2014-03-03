var blApp = angular.module('blApp', ['ngRoute', 'ngResource', 'pascalprecht.translate']);

blApp.config(['$translateProvider', function ($translateProvider) {
  $translateProvider.useMessageFormatInterpolation();
  $translateProvider.useUrlLoader('/translations');
  $translateProvider.preferredLanguage('en');
}]);

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

blApp.factory('Menu', [function () {
    var nameActive = null;
    var customActive = null;
    var subscribers = [];

    var update = function () {
        for (i in subscribers) {
            subscribers[i]();
        }
    };

    return {
        setNameActive: function (name) {
            nameActive = name;
            customActive = null;
            update();
        },
        getNameActive: function () {
            return nameActive;
        },
        setCustomActive: function (name) {
            customActive = name;
            nameActive = null;
            update();
        },
        getCustomActive: function () {
            return customActive;
        },
        register: function (callback) {
            subscribers.push(callback);
        }
    };
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

blApp.controller('ProjectListCtrl', ['$scope', '$interval', 'Menu', 'ProjectList', function ($scope, $interval, Menu, ProjectList) {
    Menu.setNameActive('projects');
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

blApp.controller('RunListCtrl', ['$scope', '$interval', 'Menu', 'RunList', function ($scope, $interval, Menu, RunList) {
    Menu.setNameActive('runs');
    $scope.count = 0;
    RunList.query().$promise.then(function (runs) {
        $scope.runs  = runs;
        $scope.count = runs.length;
    });

    var refresh = $interval(function() {
        RunList.query().$promise.then(function (runs) {
            $scope.runs = runs;
            $scope.count = runs.length;
        });
    }, 2000);

    $scope.$on('$destroy', function() {
        $interval.cancel(refresh);
    });
}]);

blApp.controller('RunShowCtrl', ['$scope', '$routeParams', '$http', '$location', '$interval', 'Menu', 'Run', 'Project', function ($scope, $routeParams, $http, $location, $interval, Menu, Run, Project) {
    Menu.setCustomActive({path: '/runs/' + $routeParams.id, label: 'Run #' + $routeParams.id});
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

blApp.controller('RunCreateCtrl', ['$scope', '$routeParams', '$location', 'Menu', 'Run', 'Project', function ($scope, $routeParams, $location, Menu, Run, Project) {
    Menu.setCustomActive({path: '/runs/create/' + $routeParams.projectName, label: $routeParams.projectName + ' - new run'});
    Project.get({name: $routeParams.projectName}).$promise.then(function (project) {
        $scope.project = project;
        for (i in project.properties) {
            $scope.run.properties[project.properties[i].name] = project.properties[i].default;
        }
    });

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

blApp.directive('blMenu', ['$translate', 'Menu', function ($translate, Menu) {
    return {
        link: function (scope, element, attrs) {
            Menu.register(function() {
                scope.locale = 'en';
                scope.changeLocale = function (locale) {
                    scope.locale = locale;
                    $translate.use(locale);
                };
                scope.nameActive = Menu.getNameActive();
                scope.customActive = Menu.getCustomActive();
            })
        }
    }
}]);
