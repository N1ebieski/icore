jQuery(document).on('click', '.destroy', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $row = $('#row' + $element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        method: 'delete',
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $element.getLoader('show');
        },
        complete: function () {
            $element.getLoader('hide');
        },
        success: function (response) {
            $row.fadeOut('slow');
        }
    });
});
