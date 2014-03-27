blApp.extend('form_factory', function (formFactory) {
    formFactory.createType('text', function (type) {
        type.setDefaultOption('invalid_message', 'validation.text.invalid');
    });

    return formFactory;
});
