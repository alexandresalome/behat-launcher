blApp.factory('FormFactory', function () {
    var FormType = function () {

    };

    FormType.prototype.buildForm = function (form) {

    };

    var Form = function () {
        this.children = {};
    };

    var FormFactory = function () {
        this.types = {};
    };

    FormFactory.prototype.createType = function(name, configCallback) {
        if (typeof this.types[name] === 'undefined') {
            throw "Type "+name+" already defined";
        }

        this.types[name] = configCallback;
    };

    FormFactory.prototype.getType = function (name) {
        if (!this.hasType(name)) {
            throw "Type " + name + " does not exist.";
        }

        if (typeof this.types[name] === 'FormType') {
            return this.types[name];
        }

        var config = this.types[name];
        this.types[name] = new FormType();
        config(this.types[name]);

        return this.types[name];
    }

    FormFactory.prototype.createForm = function (type) {
        var form = new Form();
        var type = this.getType(type);

        type.buildForm(form);
    };

    return new FormFactory();
});
