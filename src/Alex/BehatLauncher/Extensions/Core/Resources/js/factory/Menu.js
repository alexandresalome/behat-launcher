blApp.factory('Menu', function () {
    var nameActive = null;
    var customActive = null;
    var subscribers = [];

    var update = function () {
        for (i in subscribers) {
            subscribers[i]();
        }
    };

    return {
        setNameActive: function (name) {
            nameActive = name;
            customActive = null;
            update();
        },
        getNameActive: function () {
            return nameActive;
        },
        setCustomActive: function (name) {
            customActive = name;
            nameActive = null;
            update();
        },
        getCustomActive: function () {
            return customActive;
        },
        register: function (callback) {
            subscribers.push(callback);
        }
    };
});
