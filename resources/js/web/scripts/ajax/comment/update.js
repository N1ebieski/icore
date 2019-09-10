jQuery(document).on('click', 'button.updateComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'put',
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
        },
        success: function(response) {
            let $comment = $form.closest('[id^=comment]');
            $comment.html($.sanitize($(response.view).html()));
            $comment.addClass('alert-primary');
            setTimeout(function() {
                $comment.removeClassStartingWith('alert-');
            }, 5000);
        },
        error: function(response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function( key, value ) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
                return;
            }

            if (response.responseJSON.message) {
                $form.prepend($.sanitize($.getAlert(response.responseJSON.message, 'danger')));
            }
        }
    });
});
