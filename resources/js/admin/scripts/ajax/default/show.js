jQuery(document).on('click', 'a.show, button.show', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
            body: $($element.attr('data-target')).find('.modal-body'),
            content: $($element.attr('data-target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $modal.body.append('<div class="loader-absolute"><div class="spinner-grow"><span class="sr-only">Loading...</span></div></div>');
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});
