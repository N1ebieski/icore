jQuery(document).on('click', '.create', function (e) {
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
        },
        error: function (response) {
            if (response.responseJSON.message) {
                $modal.body.prepend($.getAlert('danger', response.responseJSON.message));
            }
        }        
    });
});
