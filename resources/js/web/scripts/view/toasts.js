$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/toasts@init', function () {
    $('.toast').toast('show'); 

    $(document).on('hidden.bs.toast', '.toast', function () {
        $(this).remove();
    });
});