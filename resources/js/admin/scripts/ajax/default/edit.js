$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/default@edit',
    '.edit',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $modal = $($element.data('target'));
        $modal.body = $($element.data('target')).find('.modal-body');
        $modal.footer = $($element.data('target')).find('.modal-footer');
        $modal.content = $($element.data('target')).find('.modal-content');

        $modal.on('show.bs.modal', function () {
            $(this).find('.modal-body').empty();
            $(this).find('.modal-footer').empty();
        });

        $modal.on('hidden.bs.modal', function () {
            $(this).find('.modal-body').empty();
            $(this).find('.modal-footer').empty();
        });

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            beforeSend: function () {
                $modal.body.addLoader('spinner-grow');
            },
            complete: function () {
                $modal.body.find('.loader-absolute').remove();
            },
            success: function (response) {
                $modal.content.html($.sanitize($(response.view).find('.modal-content').html()));
            }
        });
    }
);
