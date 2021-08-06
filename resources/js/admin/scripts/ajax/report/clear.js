jQuery(document).on('click', '.clearReport, .clear-report', function (e) {
    e.preventDefault();

    let $element = $(this);

    $.ajax({
        url: $element.data('route'),
        method: 'delete',
        beforeSend: function () {
            $element.getLoader('show');
        },
        complete: function () {
            $element.getLoader('hide');
        },
        success: function (response) {
            let $row = $('#row' + $element.attr('data-id'));

            $row.html($.sanitize($(response.view).html()));

            $row.addClass('alert-primary');
            setTimeout(function () {
                $row.removeClassStartingWith('alert-');
            }, 5000);

            $('.modal').modal('hide');
        }
    });
});
