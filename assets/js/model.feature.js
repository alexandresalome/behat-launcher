blApp.directive('decorateFeatureDirectory', function () {
    return {
        link : function(scope, element, attrs) {
            decorateFeatureDirectory(element);
        }
    };
});

