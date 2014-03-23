behatLauncher.formType('integer', function () {
    var IntegerFormType = function () {
        this.name = 'integer';
    }

    return new IntegerFormType();
});

behatLauncher.formType('text', function () {
    var TextFormType = function () {
        this.name = 'text';
    }

    return new TextFormType();
});

