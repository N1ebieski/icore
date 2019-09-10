jQuery(document).ready(function() {
    $('[aria-controls="collapsePublishedAt"]').change(function() {
        if ($(this).val() == 0) $('#collapsePublishedAt').collapse('hide');
        else $('#collapsePublishedAt').collapse('show');
    });
    $('[aria-controls="collapseActivationAt"]').change(function() {
        if ($(this).val() == 2) $('#collapseActivationAt').collapse('show');
        else $('#collapseActivationAt').collapse('hide');
    });
});
