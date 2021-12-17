$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/default@edit',
    '.edit',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $modal = {
            body: $($element.data('target')).find('.modal-body'),
            footer: $($element.data('target')).find('.modal-footer'),
            content: $($element.data('target')).find('.modal-content')
        };

        $modal.body.empty();
        $modal.footer.empty();

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            beforeSend: function () {
                $modal.body.append($.getLoader('spinner-grow'));
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
