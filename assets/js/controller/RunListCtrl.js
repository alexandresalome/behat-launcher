blApp.controller('RunListCtrl', ['$scope', '$timeout', 'Menu', 'RunList', function ($scope, $timeout, Menu, RunList) {
    Menu.setNameActive('runs');

    $scope.runs    = [];
    $scope.count   = 0;
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
            $scope.timeout = $timeout($scope.refreshAndTimeout, 2000);
        });
    };

    $scope.$on('$destroy', function() {
        $scope.timeout && $timeout.cancel($scope.timeout);
    });

    $scope.refreshAndTimeout();
}]);
