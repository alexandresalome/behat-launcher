blApp.factory('RunList', ['$resource', function ($resource){
    return $resource('/runs', {}, {
        query: {method:'GET', isArray:true}
    });
}]);

blApp.factory('Run', ['$resource', function ($resource){
    return $resource('/runs/:id', {}, {
        query: {method:'GET', isArray:true}
    });
}]);
