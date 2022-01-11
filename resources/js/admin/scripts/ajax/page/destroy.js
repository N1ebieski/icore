$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/page@destroy',
    '.destroyPage, .destroy-page',
    function(e) {
        e.preventDefault();

        let $element = $(this);
        let $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            method: 'delete',
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-btn-ok-class*="destroyPage"], [data-btn-ok-class*="destroy-page"]').loader('show');
            },
            complete: function () {
                $row.find('[data-btn-ok-class*="destroyPage"], [data-btn-ok-class*="destroy-page"]').loader('hide');
            },
            success: function (response) {
                $row.fadeOut('slow');

                $.each(response.descendants, function (key, value) {
                    let $rowDescendant = $('#row' + value);
                    
                    if ($rowDescendant.length) {
                        $rowDescendant.fadeOut('slow');
                    }
                });
            }
        });
    }
);
