blApp.directive('blMenu', function ($translate, localStorageService, Menu) {
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
});
