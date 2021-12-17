$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/plugins/tagsinput@init', function () {
    $('.tagsinput').each(function () {    
        let $tagsinput = $(this);

        if (!$tagsinput.parent().find('[id$="_tagsinput"]').length) {    
            $tagsinput.tagsInput({
                placeholder: $tagsinput.attr('placeholder'),
                minChars: 3,
                maxChars: $tagsinput.data('max-chars') || 30,
                limit: $tagsinput.data('max'),
                validationPattern: new RegExp('^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\u00E0-\u00FC ]+$)$'),
                unique: true,
            });
        }
    });
});
