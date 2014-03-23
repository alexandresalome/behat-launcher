blApp.factory('ProjectListResource', function ($resource) {
    return $resource('/projects', {}, {
        query: {method:'GET', isArray:true}
    });
});
