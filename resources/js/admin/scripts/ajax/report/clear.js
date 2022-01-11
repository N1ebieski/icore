$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/page@clear',
    '.clearReport, .clear-report',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        $.ajax({
            url: $element.data('route'),
            method: 'delete',
            beforeSend: function () {
                $element.loader('show');
            },
            complete: function () {
                $element.loader('hide');
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
    }
);
