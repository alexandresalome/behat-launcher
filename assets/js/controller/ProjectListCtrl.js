blApp.controller('ProjectListCtrl', ['$scope', '$timeout', 'Menu', 'ProjectList', function ($scope, $timeout, Menu, ProjectList) {
    Menu.setNameActive('projects');
    $scope.loading  = true;
    $scope.hide_project_name = true;
    $scope.projects = ProjectList.getList(function () {
        $scope.loading = false;
    });

    $scope.refresh = function () {
        ProjectList.updateList(function (projects) {
            $scope.projects = projects;
            $scope.timeout = $timeout($scope.refresh, 2000);
        });
    };

    $scope.$on('$destroy', function() {
        $scope.timeout && $timeout.cancel($scope.timeout);
    });

    $scope.refresh();
}]);
