jQuery(document).ready(function () {
    $('[aria-controls="collapse-published-at"]').change(function () {
        if ($(this).val() == 0) $('#collapse-published-at').collapse('hide');
        else $('#collapse-published-at').collapse('show');
    });
    
    $('[aria-controls="collapse-activation-at"]').change(function () {
        if ($(this).val() == 2) $('#collapse-activation-at').collapse('show');
        else $('#collapse-activation-at').collapse('hide');
    });
});
