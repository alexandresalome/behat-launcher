blApp.extend('form_factory', function (formFactory) {
    formFactory.createType('choice', function (type) {
        type.setDefaultOption('choices', null);
        type.setDefaultOption('multiple', false);
    });

    return formFactory;
});
