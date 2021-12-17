$(document).on(
    'readyAndAjax.n1ebieski/icore/admin/scripts/view/textarea@init',
    function () {
        $('textarea').each(function () {
            $(this).autoHeight({
                autogrow: $(this).data('autogrow')
            });
        });
    }
);
