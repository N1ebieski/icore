$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/default@updateStatus',
    '.status',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        let $row = $element.closest('[id^=row]');

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            data: {
                status: $element.data('status'),
            },
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $element.loader('show');
            },
            success: function (response) {
                $element.loader('hide');

                $row.html($.sanitize($(response.view).html()));

                if (response.status == 1) {
                    $row.addClass('alert-success');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);
                }

                if (response.status == 0) {
                    $row.addClass('alert-warning');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);
                }
            }
        });
    }
);
