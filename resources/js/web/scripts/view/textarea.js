$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/textarea@init', function () {
    $('textarea').each(function () {
        if ($(this).hasClass('trumbowyg-textarea')) {
            return;
        }

        $(this).autoHeight({
            autogrow: $(this).data('autogrow')
        });
    });
});
