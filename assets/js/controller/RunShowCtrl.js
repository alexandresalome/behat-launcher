blApp.controller('RunShowCtrl', function ($scope, $routeParams, $http, $location, $timeout, $window, Menu, Run) {
    Menu.setCustomActive({
        path: '/runs/' + $routeParams.id,
        label: 'Run #' + $routeParams.id
    });

    $scope.run = new Run({id: $routeParams.id});
    $scope.run.setAutorefresh(true);

    $scope.$on('run_update', function (event, data) {
        $scope.run = data.run;

        var suffix = '';
        if (run.title) {
            suffix += ': ' + run.title;
        }
        Menu.setCustomActive({
            path: '/runs/' + $routeParams.id,
            label: 'Run #' + $routeParams.id + suffix
        });
    });

    $scope.$on('$destroy', function() {
        $scope.run.setAutorefresh(false);
    });

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
});
