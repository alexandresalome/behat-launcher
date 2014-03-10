blApp.factory('Run', ['$resource', function ($resource){
    var Run = function (data) {
        this.id = data && data.id || null;
    };

    return Run;
}]);
