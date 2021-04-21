jQuery(document).ready(function() {
    $('.tagsinput').each(function () {
        $(this).tagsInput({
            placeholder: $(this).attr('placeholder'),
            minChars: 3,
            maxChars: $(this).attr('data-max-chars') || 30,
            limit: $(this).attr('data-max'),
            validationPattern: new RegExp('^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\u00E0-\u00FC ]+$)$'),
            unique: true,
        });
    });
});
