blApp.directive('blForm', function () {
    return {
        link: function (scope, element, attrs) {
            console.log("bl-form", scope, element, attrs);
        }
    }
});
