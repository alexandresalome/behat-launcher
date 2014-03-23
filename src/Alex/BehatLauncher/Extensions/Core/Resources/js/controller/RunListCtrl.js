blApp.controller('RunListCtrl', function ($scope, $timeout, Menu, RunList) {
    Menu.setNameActive('runs');

    RunList.setAutorefresh(true);

    $scope.runs = RunList.getAll();

    $scope.$on('runlist_update', function (event, data) {
        $scope.runs = data.runs;
    });

    $scope.$on('$destroy', function() {
        RunList.setAutorefresh(false);
    });
});
