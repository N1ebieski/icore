jQuery(document).on('click', '.destroyCategory, .destroy-category', function (e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row' + $element.data('id'));

    jQuery.ajax({
        url: $element.data('route'),
        method: 'delete',
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.find('[data-btn-ok-class*="destroyCategory"], [data-btn-ok-class*="destroy-category"]').getLoader('show');
        },
        complete: function () {
            $row.find('[data-btn-ok-class*="destroyCategory"], [data-btn-ok-class*="destroy-category"]').getLoader('hide');
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
});
