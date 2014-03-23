blApp.controller('ProjectListCtrl', function ($scope, $timeout, Menu, ProjectList) {
    Menu.setNameActive('projects');

    ProjectList.setAutorefresh(true);
    $scope.projects = ProjectList.getList();

    $scope.$on('projectlist_update', function (event, data) {
        $scope.projects = data.projects;
    });

    $scope.$on('$destroy', function() {
        ProjectList.setAutorefresh(false);
    });
});
