$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/view/toasts@init', function () {
    $('.toast').toast('show');

    $('.toast').on('hidden.bs.toast', function () {
        $(this).remove();
    });    
});
