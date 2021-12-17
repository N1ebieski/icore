$(function () {
    $(document).trigger('ready');
    $(document).trigger('readyAndAjax');
});

$(document).ajaxComplete(function () {
    $(document).trigger('readyAndAjax');
});

$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/actions@enter', function () {
    $('form').find('input, select')
        .on('keypress', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                
                return false;
            }
        });
});

$(window).on('readyAndAjax.n1ebieski/icore/admin/scripts/actions@focusSpellcheck', function () {
    if (navigator.userAgent.indexOf("Firefox") != -1) {
        $('[spellcheck="true"]:first').focusWithoutScrolling();
    }
});
