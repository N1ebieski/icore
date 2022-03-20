$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/clipboard@init', function () {
    $(".clipboard").each(function () {
        $(this).clipboard({
            lang: $(this).data('lang')
        });
    });
});