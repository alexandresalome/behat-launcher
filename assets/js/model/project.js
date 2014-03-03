blApp.factory('ProjectList', ['$resource', function ($resource){
    return $resource('/projects', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('Project', ['$resource', function ($resource){
    return $resource('/projects/:name', {}, {
        query: {method:'GET', isArray:true}
    });
}]);
