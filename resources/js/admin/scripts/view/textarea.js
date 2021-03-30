jQuery(document).on('readyAndAjax', function () {
    $('textarea').each(function () {
        $(this).autoHeight({
            autogrow: $(this).data('autogrow')
        });
    });
});
