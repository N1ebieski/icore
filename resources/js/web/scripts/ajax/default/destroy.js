$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/default@destroy',
    '.destroy',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            method: 'delete',
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-btn-ok-class*="destroy"]').getLoader('show');
            },
            complete: function () {
                $row.find('[data-btn-ok-class*="destroy"]').getLoader('hide');
            },
            success: function (response) {
                $row.fadeOut('slow');
            }
        });
    }
);
