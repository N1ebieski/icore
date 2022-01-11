$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/report@store',
    '.storeReport, .store-report',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form');
        $form.btn = $form.find('.btn');
        $form.input = $form.find('.form-control');

        $.ajax({
            url: $form.data('route'),
            method: 'post',
            data: $form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $element.loader('show');
                $('.invalid-feedback').remove();
                $form.input.removeClass('is-valid');
                $form.input.removeClass('is-invalid');
            },
            complete: function () {
                $element.loader('hide');
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                $('.modal').modal('hide');

                $('body').addToast(response.success);
            },
            error: function (response) {
                let errors = response.responseJSON;

                $.each(errors.errors, function (key, value) {
                    $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                    $form.find('#' + $.escapeSelector(key)).closest('.form-group').addError({
                        id: key,
                        message: value
                    });
                });
            }
        });
    }
);
