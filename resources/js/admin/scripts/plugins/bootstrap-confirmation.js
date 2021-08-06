jQuery(document).on('readyAndAjax', function () {
    $('[data-toggle=confirmation]').each(function () {
        let $confirmation = $(this);

        $confirmation.confirmation({
            rootSelector: '[data-toggle=confirmation]',
            copyAttributes: 'href data-route data-id',
            singleton: true,
            popout: true,      
            onConfirm: function() {
                if ($confirmation.hasClass('submit')) {
                    $confirmation.parents('form:first').submit();
                }
            }
        });
    });
});
