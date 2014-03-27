blApp.controller('ProjectCreateCtrl', function ($scope, $timeout, Menu, Project) {
    Menu.setCustomActive({
        path: '/create-project',
        label: 'New project'
    });

    $scope.project  = new Project();
    $scope.disabled = false;

    $scope.submit = function () {
        console.log($scope.project);
        $scope.disabled = true;
    };
});
