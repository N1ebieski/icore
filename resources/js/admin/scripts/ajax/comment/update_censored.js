$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/comment@updateCensored',
    '.censoreComment, .censore-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        let $row = $element.closest('[id^=row]');

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            data: {
                censored: $element.data('censored'),
            },
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $element.loader('show');
            },
            success: function (response) {
                $element.loader('hide');

                $row.html($.sanitize($(response.view).html()));

                if (response.censored == 1) {
                    $row.addClass('alert-warning');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);
                }

                if (response.censored == 0) {
                    $row.addClass('alert-success');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);
                }
            }
        });
    }
);
