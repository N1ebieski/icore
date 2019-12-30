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

jQuery(window).on('readyAndAjax', function() {
    if (navigator.userAgent.indexOf("Firefox") != -1) {
        $('[spellcheck="true"]:first').focusWithoutScrolling();
    }
});
