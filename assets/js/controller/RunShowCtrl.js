blApp.controller('RunShowCtrl', ['$scope', '$routeParams', '$http', '$location', '$timeout', '$window', 'Menu', 'Run', 'Project', function ($scope, $routeParams, $http, $location, $timeout, $window, Menu, Run, Project) {
    Menu.setCustomActive({
        path: '/runs/' + $routeParams.id,
        label: 'Run #' + $routeParams.id
    });

    $scope.loading = true;

    $scope.refresh = function(callback) {
        Run.get({id: $routeParams.id}).$promise.then(function (run) {
            $scope.loading  = false;
            $scope.run = run;
            callback();
        });
    };

    $scope.refreshAndTimeout = function () {
        $scope.refresh(function () {
            $scope.timeout = $timeout($scope.refreshAndTimeout, 2000);
        });
    };

    $scope.$on('$destroy', function() {
        $scope.timeout && $timeout.cancel($scope.timeout);
    });

    $scope.refreshAndTimeout();

    $scope.goBack = function () {
         $window.history.back();
    }

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
