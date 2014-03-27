blApp.factory('Project', function ($resource, $rootScope, $interval){

    var $resource = $resource('/projects/:id', {}, {
        query: {method:'GET', isArray:true}
    });

    var Project = function (data) {
        this.autorefresh = false;
        this.id          = null;
        this.name        = null;
        this.properties  = [];

        this.hydrate(data);

        if (this.id) {
            this.update();
        }
    };

    Project.prototype.setAutorefresh = function (autorefresh) {
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

    Project.prototype.update = function () {
        var self = this;
        $resource.get({id: this.id}).$promise.then(function (data) {
            self.hydrate(data);
        });
    };

    Project.prototype.hydrate = function (data) {
        if (data && typeof data.id !== 'undefined') {
            this.id = data.id;
        }

        if (data && typeof data.name !== 'undefined') {
            this.name = data.name;
        }

        if (data && typeof data.properties !== 'undefined') {
            this.properties = data.properties;
        }
    };

    Project.prototype.toArray = function () {
        return {
            id:   this.id,
            name: this.name,
            properties: this.properties
        };
    };

    Project.prototype.save = function (callback) {
        var self = this;
        var $res = new $resource(this.toArray());

        $res.$save(function (msg, headers) {
            self.hydrate(msg);
            $rootScope.$broadcast('project_update', {project: self});

            callback(self);
        });
    };

    return Project;
});
