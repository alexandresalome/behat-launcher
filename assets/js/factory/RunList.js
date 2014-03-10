blApp.factory('RunList', ['$resource', function ($resource){
    return $resource('/runs', {}, {
        query: {method:'GET', isArray:true}
    });
}]);
