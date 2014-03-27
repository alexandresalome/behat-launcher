blApp.controller('ProjectCreateCtrl', function ($scope, $timeout, Menu, Project, FormFactory) {
    Menu.setCustomActive({
        path: '/create-project',
        label: 'New project'
    });

    $scope.project  = new Project();
    $scope.disabled = false;
    $scope.form     = FormFactory.create('project');

    $scope.submit = function () {
        console.log($scope.project);
        $scope.disabled = true;
    };
});
