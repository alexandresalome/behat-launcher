blApp.factory('RunList', function ($resource, $interval, $rootScope){
    var $resource = $resource('/runs', {}, {
        query: {method:'GET', isArray:true}
    });

    var RunList = function (data) {
        this.runs = null;
        this.update();
    };

    RunList.prototype.setAutorefresh = function (autorefresh) {
        this.autorefresh = autorefresh;
        var self = this;

        if (this.autorefresh && !this.interval) {
            this.interval = $interval(function () {
                self.update();
            }, 2000);
        } else if (!this.autorefresh && this.interval) {
            $interval.cancel(this.interval);
            this.interval = null;
        }
    };

    RunList.prototype.hydrate = function (data) {
        this.runs = data;
        $rootScope.$broadcast('runlist_update', {runs: this.runs});
    };

    RunList.prototype.getAll = function () {
        return this.runs;
    };

    RunList.prototype.update = function () {
        var self = this;
        $resource.query().$promise.then(function (data) {
            self.hydrate(data);
        });
    };

    return new RunList();
});
