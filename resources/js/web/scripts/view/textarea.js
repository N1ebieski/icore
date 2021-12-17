$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/textarea@init', function () {
    $('textarea').each(function () {
        $(this).autoHeight({
            autogrow: $(this).data('autogrow')
        });
    });
});
