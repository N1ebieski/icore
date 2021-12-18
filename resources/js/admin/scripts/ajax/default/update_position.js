$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/default@updatePosition',
    '.updatePosition, .update-position',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form');

        $.ajax({
            url: $form.data('route'),
            method: 'patch',
            data: {
                position: $form.find('#position').val(),
            },
            beforeSend: function () {
                $element.getLoader('show');
            },
            complete: function () {
                $element.getLoader('hide');
            },
            success: function (response) {
                $('.modal').modal('hide');

                $.each(response.siblings, function (key, value) {
                    let $rowSibling = $('#row' + key);

                    if ($rowSibling.length) {
                        $rowSibling.find('#position').text(value + 1);

                        $rowSibling.addClass('alert-primary');
                        setTimeout(function () {
                            $rowSibling.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }
        });
    }
);
