jQuery(document).on('click', '.storeBanModel, .store-banmodel', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $form = $element.closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control, .custom-control-input');
    
    let $modal = {
        body: $form.closest('.modal-body')
    };

    jQuery.ajax({
        url: $form.attr('data-route'),
        method: 'post',
        data: $form.serialize(),
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
            $modal.body.html($.getAlert('success', response.success));
        },
        error: function (response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function (key, value) {
                    $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                    $form.find('#' + $.escapeSelector(key)).closest('.form-group').append($.getError(key, value));
                });
            }
        }
    });
});
