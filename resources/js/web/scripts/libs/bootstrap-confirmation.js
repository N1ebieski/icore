jQuery(document).ready(function() {
    $('[data-toggle=confirmation]').confirmation({
        rootSelector: '[data-toggle=confirmation]',
        singleton: true,
        popout: true,
        onConfirm: function() {
            if ($(this).hasClass('submit')) {
        		$(this).parents('form:first').submit();
            }
        }
    });
});
