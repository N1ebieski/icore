jQuery(document).on('click', 'button.storeComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
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
            $form.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $form.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
            $form.find('.captcha').recaptcha();
            $form.find('.captcha').captcha();
        },
        success: function(response) {
            if (response.view) {
                $form.closest('[id^=comment]').after($.sanitize(response.view));

                let $comment = $form.closest('[id^=comment]').next('div');

                $comment.addClass('alert-primary font-italic border-bottom');
                setTimeout(function() {
                    $comment.removeClassStartingWith('alert-');
                }, 5000);
            }

            if (response.success) {
                $form.before($.getAlert(response.success, 'success'));
            }

            if ($form.find('#parent_id').val() != 0) {
                $form.remove();
            } else {
                $form.find('#content').val('');
            }
        },
        error: function(response) {

            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function(key, value) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
                return;
            }

            if (response.responseJSON.message) {
                $form.prepend($.getAlert(response.responseJSON.message, 'danger'));
            }
        }
    });
});
