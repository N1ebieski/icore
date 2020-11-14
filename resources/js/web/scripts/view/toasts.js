jQuery(document).on('readyAndAjax', function () {
    $('.toast').toast('show');

    $('.toast').on('hidden.bs.toast', function () {
        $(this).remove();
    });    
});