jQuery(document).ready(function() {
    $(document).trigger('readyAndAjax');
});

jQuery(document).ajaxComplete(function() {
    $(document).trigger('readyAndAjax');
});
