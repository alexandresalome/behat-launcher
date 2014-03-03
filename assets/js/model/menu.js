blApp.factory('Menu', [function () {
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
}]);

blApp.directive('blMenu', ['$translate', 'localStorageService', 'Menu', function ($translate, localStorageService, Menu) {
    return {
        link: function (scope, element, attrs) {
            Menu.register(function() {
                scope.locale = localStorageService.get('locale');
                if (!scope.locale) {
                    scope.locale = 'en';
                }
                scope.changeLocale = function (locale) {
                    localStorageService.add('locale', locale);
                    scope.locale = locale;
                    $translate.use(locale);
                };
                scope.nameActive = Menu.getNameActive();
                scope.customActive = Menu.getCustomActive();
            })
        }
    }
}]);
