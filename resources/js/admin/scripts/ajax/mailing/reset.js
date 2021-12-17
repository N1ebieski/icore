$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/mailing@reset',
    'a.resetMailing, a.reset-mailing',
    function (e) {
        e.preventDefault();

        var $element = $(this);
        var $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            method: 'delete',
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-btn-ok-class*="resetMailing"], [data-btn-ok-class*="reset-mailing"]').getLoader('show');
            },
            complete: function () {
                $row.find('[data-btn-ok-class*="resetMailing"], [data-btn-ok-class*="reset-mailing"]').getLoader('hide');
            },
            success: function (response) {
                $row.html($.sanitize($(response.view).html()));

                $row.addClass('alert-danger');
                setTimeout(function () {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        });
    }
);
