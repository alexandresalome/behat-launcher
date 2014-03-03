blApp.controller('RunListCtrl', ['$scope', '$interval', 'Menu', 'RunList', function ($scope, $interval, Menu, RunList) {
    Menu.setNameActive('runs');

    $scope.loading = true;

    $scope.refresh = function(callback) {
        RunList.query().$promise.then(function (runs) {
            $scope.loading  = false;
            $scope.runs     = runs;
            $scope.count    = runs.length;
            callback();
        });
    };

    $scope.refreshAndTimeout = function () {
        $scope.refresh(function () {
            $timeout($scope.refreshAndTimeout, 2000);
        });
    };

    $scope.$on('$destroy', function() {
        $scope.timeout && $timeout.cancel($scope.timeout);
    });


    RunList.query().$promise.then(function (runs) {
        $scope.runs    = runs;
        $scope.count   = runs.length;
        $scope.loading = false;
    });

    $scope.timeout = refresh = $interval(function() {
        RunList.query().$promise.then(function (runs) {
            $scope.runs = runs;
            $scope.count = runs.length;
        });
    }, 2000);

    $scope.$on('$destroy', function() {
        $interval.cancel(refresh);
    });
}]);
