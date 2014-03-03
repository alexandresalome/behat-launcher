blApp.controller('ProjectListCtrl', ['$scope', '$timeout', 'Menu', 'ProjectList', function ($scope, $timeout, Menu, ProjectList) {
    Menu.setNameActive('projects');

    $scope.loading  = true;

    $scope.refresh = function(callback) {
        ProjectList.query().$promise.then(function (projects) {
            $scope.loading  = false;
            $scope.projects = projects;
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
