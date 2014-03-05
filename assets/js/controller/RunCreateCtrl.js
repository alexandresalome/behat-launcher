blApp.controller('RunCreateCtrl', ['$scope', '$location', '$routeParams', 'Menu', 'Run', 'Project', 'ProjectList', function ($scope, $location, $routeParams, Menu, Run, Project, ProjectList) {
    Menu.setNameActive('create');

    $scope.run = {
        title: null,
        projectName: null,
        properties: {},
        features: {}
    };

    $scope.loading = true;
    $scope.projects = ProjectList.query();
    $scope.projects.$promise.then(function (projects) {
        if ($routeParams.project) {
            for (k in projects) {
                if (projects[k].name == $routeParams.project) {
                    $scope.loadProject(projects[k]);

                    return;
                }
            }

            $location.url('/create');
        } else {
            for (k in projects) {
                $scope.loadProject(projects[k]);

                return;
            }
        }
    });

    $scope.loadProject = function (project) {
        $scope.project = project;
        $scope.loading = false;
        $scope.run.projectName = project.name;
        $location.search('project', project.name);
        $scope.run.features = {};
        $scope.run.properties = {};
        for (i in project.properties) {
            $scope.run.properties[project.properties[i].name] = project.properties[i].default;
        }
    };

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
