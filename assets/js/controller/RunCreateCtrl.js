blApp.controller('RunCreateCtrl', function ($scope, $location, $routeParams, Menu, Run, ProjectList) {
    Menu.setNameActive('create');

    $scope.projects          = ProjectList.getList();
    $scope.projectListChoice = null;
    $scope.selectedFeatures  = {};
    $scope.project           = null;
    $scope.projectName       = null;

    $scope.run = new Run({
        projectName: $scope.projectName
    });

    $scope.$on('projectlist_update', function (event, data) {
        $scope.projects = data.projects;
        $scope.syncProjectList($scope.projectName);
    });

    $scope.$on('project_update', function (event, data) {
        if (data.project.name === $scope.projectName) {
            $scope.project = data.project;
        }
    });

    $scope.hydrateRunFromProject = function () {
        $scope.run.projectName = $scope.project.name;
        $location.search('project', $scope.project.name);
        for (i in project.properties) {
            if (!$scope.run.properties[project.properties[i].name]) {
                $scope.run.properties[project.properties[i].name] = project.properties[i].default;
            }
        }
    };

    $scope.syncProjectList = function () {
        for (i in $scope.projects) {
            if ($scope.projects[i].name == $scope.projectName) {
                $scope.projectListChoice = $scope.projects[i];
            }
        }
    };

    $scope.setProjectName = function (name) {
        $scope.run.projectName = name;
        $scope.projectName     = name;
        $scope.project         = ProjectList.get(name);
        $scope.syncProjectList();
        $location.search('project', name);
    };

    $scope.submit = function () {
        $scope.run.features = [];

        for (k in $scope.selectedFeatures) {
            if ($scope.selectedFeatures[k] && $scope.selectedFeatures[k]) {
                $scope.run.features.push(k);
            }
        }

        if ($scope.run.features.length == 0) {
            $scope.error = 'form.run.error.no_feature';

            return;
        }

        $scope.disabled = true;

        $scope.run.save(function (run) {
            $location.url('/runs/' + run.id);
        });
    };

    if ($routeParams.project) {
        $scope.setProjectName($routeParams.project);
    }
});
