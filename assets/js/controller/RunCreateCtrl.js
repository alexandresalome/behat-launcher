blApp.controller('RunCreateCtrl', ['$scope', '$routeParams', '$location', 'Menu', 'Run', 'Project', function ($scope, $routeParams, $location, Menu, Run, Project) {
    Menu.setCustomActive({
        path:  '/runs/create/' + $routeParams.projectName,
        label: $routeParams.projectName + ' - new run'
    });

    $scope.loading = true;
    $scope.run = {
        title: null,
        projectName: $routeParams.projectName,
        properties: {},
        features: {}
    };

    Project.get({name: $routeParams.projectName}).$promise.then(function (project) {
        $scope.loading = false;
        $scope.project = project;
        for (i in project.properties) {
            $scope.run.properties[project.properties[i].name] = project.properties[i].default;
        }
    });

    $scope.submit = function () {
        // convert array of booleans to array of strings
        var features = $scope.run.features;
        $scope.run.features = [];
        for (i in features) {
            $scope.run.features.push(i);
        }

        if ($scope.run.features.length == 0) {
            $scope.error = 'form.run.error.no_feature';

            return;
        }

        var run = new Run($scope.run);
        run.$save(function (msg, headers) {
            $location.path('/runs/' + msg.id);
        });
    };
}]);
