blApp.controller('RunCreateCtrl', ['$scope', '$location', '$routeParams', 'Menu', 'Run', 'ProjectList', function ($scope, $location, $routeParams, Menu, Run, ProjectList) {
    Menu.setNameActive('create');

    $scope.run = {
        title: null,
        projectName: null,
        properties: {},
        features: {}
    };

    $scope.loading = true;

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

    $scope.loadProjectFromName = function (name) {
        $scope.loading = true;
        ProjectList.get(name, function (project) {
            $scope.loading = false;
            $scope.loadProject(project);
        });
    };

    ProjectList.getList(function (projects) {
        $scope.projects = projects;
        if ($routeParams.project) {
            for (k in projects) {
                if (projects[k].name == $routeParams.project) {
                    $scope.projectListChoice = projects[k];
                }
            }
        } else {
            $scope.projectListChoice = projects[0];
        }

        $scope.loadProjectFromName($scope.projectListChoice.name);
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
