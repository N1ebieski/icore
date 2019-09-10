jQuery(document).on('click', '.storeNewsletter', function(e) {
    e.preventDefault();

    let $form = $(this).parents('form');
    $form.btn = $form.find('.btn');
    $form.group = $form.find('.form-group');
    $form.input = $form.find('.form-control');

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $form.group.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $('.valid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $form.group.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            if (response.success) {
                $form.find('[name="email"]').val('');
                $form.find('[name="email"]').closest('.form-group').append($.getMessage(response.success));
            }
        },
        error: function(response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function( key, value ) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
            }
        }
    });
});
