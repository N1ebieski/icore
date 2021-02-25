jQuery(document).on('readyAndAjax', function () {
    $textarea = $('textarea');

    $textarea.autoHeight({
        autogrow: $textarea.data('autogrow')
    });
});