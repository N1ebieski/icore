jQuery(document).on('readyAndAjax', function() {
    $('[data-toggle=confirmation]').confirmation({
        rootSelector: '[data-toggle=confirmation]',
        copyAttributes: 'data-route data-id',
        singleton: true,
        popout: true,
        onConfirm: function() {
            if ($(this).hasClass('submit')) {
        		$(this).parents('form:first').submit();
            }
        }
    });
});
