jQuery(document).on('click', 'a.createReport, a.create-report', function(e) {
    e.preventDefault();

    let $element = $(this);

    let $modal = {
        body: $($element.attr('data-target')).find('.modal-body'),
        content: $($element.attr('data-target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.data('route'),
        method: 'get',
        beforeSend: function () {
            $modal.body.html($.getLoader('spinner-grow'));
        },
        complete: function () {
            $modal.content.find('.loader-absolute').remove();
        },
        success: function (response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});
