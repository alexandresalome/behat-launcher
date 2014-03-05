blApp.factory('ProjectList', ['$resourceProjectList', '$resourceProject', function ($resourceProjectList, $resourceProject) {
    var projectList = null;
    var projects = {};

    var doUpdateList = function (callback) {
        $resourceProjectList.query().$promise.then(function (projects) {
            projectList = projects;
            callback(projects);
        });
    };

    var doUpdateOne = function (name, callback) {
        projects[name] = $resourceProject.get({name: name});
        projects[name].$promise.then(callback);

        return projects[name];
    };

    return {
        getList: function (callback) {
            if (!projectList) {
                return doUpdateList(callback);
            }

            projectList.$promise.then(callback);

            return projectList
        },
        get: function (name, callback) {
            if (!projects[name]) {
                return doUpdateOne(name, callback);
            }

            projects[name].$promise.then(callback);

            return projects[name];
        },
        updateList: function (callback) {
            return doUpdateList(callback);
        },
        updateOne: function (name, callback) {
            return doUpdateOne(name, callback);
        }
    };
}]);

// $resource services

blApp.factory('$resourceProjectList', ['$resource', function ($resource) {
    return $resource('/projects', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('$resourceProject', ['$resource', function ($resource) {
    return $resource('/projects/:name', {}, {
        query: {method:'GET', isArray:true}
    });
}]);
