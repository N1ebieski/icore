jQuery(document).on('click', '.storeReport, .store-report', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $form = $element.closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');

    let $modal = {
        body: $form.closest('.modal-body')
    };

    $.ajax({
        url: $form.data('route'),
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
            let errors = response.responseJSON;

            $.each(errors.errors, function (key, value) {
                $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                $form.find('#' + $.escapeSelector(key)).after($.getError(key, value));
            });
        }
    });
});
