jQuery(document).on('click', '.update', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');
    let $modal = {
        body: $form.closest('.modal-body')
    };

    let data = new FormData($form[0]);
    data.append('_method', 'put');

    jQuery.ajax({
        url: $form.attr('data-route'),
        method: 'post',
        // data: $form.serialize(),
        data: data,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
            $form.find('.invalid-feedback').remove();
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
            $row.html($.sanitize($(response.view).html()));

            $row.addClass('alert-primary');
            setTimeout(function() {
                $row.removeClassStartingWith('alert-');
            }, 5000);
            $('.modal').modal('hide');
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                $form.find('#' + $.escapeSelector(key)).parent().append($.getError(key, value));
            });
        }
    });
});
