jQuery(document).on('click', 'button.storeComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');
    let $modal = {};
    $modal.body = $form.closest('.modal-body');

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
            $modal.body.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $modal.body.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            let $row = $('#row'+$form.attr('data-id'));
            $row.after($.sanitize(response.view));

            let $rowNext = $row.next();
            $rowNext.addClass('alert-primary font-italic');
            setTimeout(function() {
                $rowNext.removeClassStartingWith('alert-');
            }, 5000);
            $('.modal').modal('hide');
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $form.find('#'+key).addClass('is-invalid');
                $form.find('#'+key).after($.getError(key, value));
            });
        }
    });
});
