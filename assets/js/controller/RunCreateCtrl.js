blApp.controller('RunCreateCtrl', function ($scope, $location, $routeParams, Menu, Run, ProjectList) {
    Menu.setNameActive('create');

    $scope.projects    = ProjectList.getList();
    $scope.project     = null;
    $scope.projectName = null;

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

    $scope.syncProjectList = function (name) {
        for (i in $scope.projects) {
            if ($scope.projects[i].name == name) {
                $scope.projectListChoice = $scope.projects[i];
            }
        }
    };

    $scope.setProjectName = function (name) {
        $scope.projectName = name;
        $scope.project = ProjectList.get(name);
        $scope.syncProjectList(name);
        $location.search('project', name);
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

        $scope.disabled = true;
    };

    if ($routeParams.project) {
        $scope.setProjectName($routeParams.project);
    }
});
