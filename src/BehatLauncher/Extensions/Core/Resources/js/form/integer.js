blApp.extend('form_factory', function (formFactory) {
    formFactory.createType('integer', function (type) {
        type.setDefaultOption('invalid_message', 'validation.integer.invalid');
    });

    return formFactory;
});
