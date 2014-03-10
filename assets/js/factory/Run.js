blApp.factory('Run', function ($resource, $rootScope, $interval){

    var $resource = $resource('/runs/:id', {}, {
        query: {method:'GET', isArray:true}
    });

    var Run = function (data) {
        this.autorefresh = false;
        this.id          = null;
        this.title       = null;
        this.running     = null;
        this.projectName = null;
        this.properties  = {};
        this.features    = [];
        this.units       = [];
        this.status      = '';
        this.progress    = {};
        this.count       = {};

        this.hydrate(data);

        if (this.id) {
            this.update();
        }
    };

    Run.prototype.setAutorefresh = function (autorefresh) {
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

    Run.prototype.update = function () {
        var self = this;
        $resource.get({id: this.id}).$promise.then(function (data) {
            self.hydrate(data);
        });
    };

    Run.prototype.hydrate = function (data) {
        if (data && typeof data.id !== 'undefined') {
            this.id = data.id;
        }

        if (data && typeof data.title !== 'undefined') {
            this.title = data.title;
        }

        if (data && typeof data.running !== 'undefined') {
            this.running = data.running;
        }

        if (data && typeof data.projectName !== 'undefined') {
            this.projectName = data.projectName;
        }

        if (data && typeof data.properties !== 'undefined') {
            this.properties = data.properties;
        }

        if (data && typeof data.features !== 'undefined') {
            this.features = data.features;
        }

        if (data && typeof data.units !== 'undefined') {
            this.units = data.units;
        }

        if (data && typeof data.status !== 'undefined') {
            this.status = data.status;
        }

        if (data && typeof data.progress !== 'undefined') {
            this.progress = data.progress;
        }

        if (data && typeof data.count !== 'undefined') {
            this.count = data.count;
        }
    };

    Run.prototype.toArray = function () {
        return {
            id: this.id,
            title: this.title,
            projectName: this.projectName,
            properties: this.properties,
            features: this.features
        };
    };

    Run.prototype.save = function (callback) {
        var self = this;
        var $res = new $resource(this.toArray());

        $res.$save(function (msg, headers) {
            self.hydrate(msg);
            $rootScope.$broadcast('run_update', {run: self});

            callback(self);
        });
    };

    return Run;
});
