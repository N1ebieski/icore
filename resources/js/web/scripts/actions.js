jQuery(document).ready(function() {
    $(document).trigger('readyAndAjax');
});

jQuery(document).ajaxComplete(function() {
    $(document).trigger('readyAndAjax');
});

jQuery(document).on('readyAndAjax', function() {
    $('form').find('input, select').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            return false;
        }
    });
});
