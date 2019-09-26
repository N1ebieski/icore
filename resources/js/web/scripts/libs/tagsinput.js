jQuery(document).ready(function() {
    $('.tagsinput').tagsInput({
        placeholder: $('.tagsinput').attr('placeholder'),
        minChars: 3,
        maxChars: 30,
        limit: $('.tagsinput').attr('data-max'),
        validationPattern: new RegExp('^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\u00E0-\u00FC ]+$)$'),
        unique: true,
    });
});
