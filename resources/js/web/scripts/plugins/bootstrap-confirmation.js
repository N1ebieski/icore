jQuery(document).on('readyAndAjax', function () {
    $('[data-toggle=confirmation]').each(function () {
        $(this).confirmation({
            rootSelector: '[data-toggle=confirmation]',
            copyAttributes: 'href data-route data-id',
            singleton: true,
            popout: true,
            onConfirm: function() {
                if ($(this).hasClass('submit')) {
                    $(this).parents('form:first').submit();
                }
            }
        });
    });
});
