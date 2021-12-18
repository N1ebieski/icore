$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/comment@store',
    '.storeComment, .store-comment',
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
                let $row = $('#row' + $form.data('id'));
                $row.after($.sanitize(response.view));

                let $rowNext = $row.next();
                $rowNext.addClass('alert-primary font-italic');
                setTimeout(function() {
                    $rowNext.removeClassStartingWith('alert-');
                }, 5000);

                $('.modal').modal('hide');
            },
            error: function(response) {
                $.each(response.responseJSON.errors, function (key, value) {
                    $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                    $form.find('#' + $.escapeSelector(key)).after($.getError(key, value));
                });
            }
        });
    }
);
