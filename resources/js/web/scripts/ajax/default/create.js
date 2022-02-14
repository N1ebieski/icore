$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/default@create',
    '.create',
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
                $modal.body.addLoader('spinner-grow');
            },
            complete: function () {
                $modal.body.find('.loader-absolute').remove();
            },
            success: function (response) {
                $modal.content.html($.sanitize($(response.view).find('.modal-content').html()));
            },
            error: function (response) {
                if (response.responseJSON.message) {
                    $('body').addToast({
                        title: response.responseJSON.message,
                        type: 'danger'
                    });
                }
            }        
        });
    }
);
