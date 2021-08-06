jQuery(document).on('click', '.storeNewsletter, .store-newsletter', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $form = $element.parents('form');
    $form.btn = $form.find('.btn');
    $form.group = $form.find('.form-group');
    $form.input = $form.find('.form-control, .custom-control-input');

    jQuery.ajax({
        url: $form.data('route'),
        method: 'post',
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function () {
            $element.getLoader('show');
            $('.invalid-feedback').remove();
            $('.valid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function () {
            $element.getLoader('hide');
            $form.input.addClass('is-valid');
        },
        success: function (response) {
            if (response.success) {
                $form.find('[name="email"]').val('');
                $form.find('[name="email"]').closest('.form-group').append($.getMessage(response.success));
            }
        },
        error: function (response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function (key, value) {
                    $form.find('[name="' + key + '"]').addClass('is-invalid');
                    $form.find('[name="' + key + '"]').closest('.form-group').append($.getError(key, value));
                });
            }
        }
    });
});
