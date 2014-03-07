blApp.controller('RunCreateCtrl', function ($scope, $location, $routeParams, Menu, Run, ProjectList) {
    Menu.setNameActive('create');

    $scope.projects = ProjectList.getList();
    $scope.run = new Run();

    $scope.$on('projectlist_update', function (event, data) {
        $scope.projects = data.projects;
    });

    return;

    $scope.$on('project_update', function (event, data) {
        if ($scope.project && data.project.name === $scope.project.name) {
            $scope.project = data.project;
        }
    });

    $scope.loadProject = function () {
        $scope.run.projectName = project.name;
        $location.search('project', project.name);
        for (i in project.properties) {
            if (!$scope.run.properties[project.properties[i].name]) {
                $scope.run.properties[project.properties[i].name] = project.properties[i].default;
            }
        }
    };

    $scope.loadProjectFromName = function (name) {
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

    $scope.disabled = false;
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

        $scope.disabled = true;
    };
});
