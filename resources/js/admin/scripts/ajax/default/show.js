jQuery(document).on('click', 'a.show, button.show', function (e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
            body: $($element.data('target')).find('.modal-body'),
            content: $($element.data('target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.data('route'),
        method: 'get',
        beforeSend: function () {
            $modal.body.append($.getLoader('spinner-grow'));
        },
        complete: function () {
            $modal.body.find('.loader-absolute').remove();
        },
        success: function (response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});
