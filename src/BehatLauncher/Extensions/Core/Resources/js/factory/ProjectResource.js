blApp.factory('ProjectResource', function ($resource) {
    return $resource('/projects/:name', {}, {
        query: {method:'GET', isArray:true}
    });
});
