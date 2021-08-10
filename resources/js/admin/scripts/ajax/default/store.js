jQuery(document).on('click', '.store', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $form = $element.closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');

    jQuery.ajax({
        url: $form.data('route'),
        method: 'post',
        data: new FormData($form[0]),
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            $element.getLoader('show');
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function () {
            $element.getLoader('hide');
            $form.input.addClass('is-valid');
        },
        success: function (response) {
            $('.modal').modal('hide');
            
            window.location.reload();
        },
        error: function (response) {
            if (response.responseJSON.errors) {
                let i = 0;

                $.each(response.responseJSON.errors, function (key, value) {
                    key = key.match(/([a-z_\-\.]+)(?:\.([\d]+)|)$/)[1];

                    $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                    $form.find('#' + $.escapeSelector(key)).closest('.form-group').append($.getError(key, value));

                    if (i === 0 && $('#' + $.escapeSelector(key)).length) {
                        $('.modal').animate({
                            scrollTop: $('#' + $.escapeSelector(key)).position().top + 50
                        }, 1000);
                    }

                    i++;
                });

                return;                    
            }
            
            if (response.responseJSON.message) {
                $form.prepend($.getAlert(response.responseJSON.message, 'danger'));
            }
        }
    });
});
