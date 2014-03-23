blApp.factory('ProjectList', function ($rootScope, $interval, ProjectListResource, ProjectResource) {
    var ProjectList = function () {
        this.autorefresh = false;
        this.interval = null;
        this.projectList = null;
        this.projects = {};
    };

    ProjectList.prototype.setAutorefresh = function (autorefresh) {
        this.autorefresh = autorefresh;

        if (this.autorefresh && !this.interval) {
            this.interval = $interval(this.updateList, 2000);
        } else if (!this.autorefresh && this.interval) {
            $interval.cancel(this.interval);
            this.interval = null;
        }
    };

    /**
     * Returns list of projects as an array if immediatly available,
     * null if not loaded yet.
     *
     * @return array|null an array of Project or null
     */
    ProjectList.prototype.getList = function () {
        if (!this.projectList) {
            this.updateList();
        }

        return this.projectList;
    };

    /**
     * Returns a single project.
     *
     * @param  string name
     * @return Project|null
     */
    ProjectList.prototype.get = function (name) {
        if (!this.projects[name]) {
            this.updateOne(name);

            return null;
        }

        return this.projects[name];

    };

    /**
     * Request update of projects list.
     *
     * @return void
     */
    ProjectList.prototype.updateList = function () {
        var self = this;
        ProjectListResource.query().$promise.then(function (projects) {
            self.projectList = projects;
            $rootScope.$broadcast('projectlist_update', {projects: projects});
        });
    };

    /**
     * Request update of a project.
     *
     * @return void
     */
    ProjectList.prototype.updateOne = function (name) {
        var self = this;
        ProjectResource.get({name: name}).$promise.then(function (project) {
            self.projects[name] = project;
            $rootScope.$broadcast('project_update', {project: project});
        });
    };

    return new ProjectList();
});
