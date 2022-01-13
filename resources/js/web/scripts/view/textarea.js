$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/textarea@init', function () {
    $('textarea').each(function () {
        if ($(this).hasClass('trumbowyg-textarea') || $(this).is('[id*=trumbowyg]')) {
            return;
        }

        $(this).autoHeight({
            autogrow: $(this).data('autogrow')
        });
    });
});
