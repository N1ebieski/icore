jQuery(document).on('click', 'button.clearReport', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
        body: $element.closest('.modal').find('.modal-body'),
        content: $element.closest('.modal').find('.modal-content')
    };

    $.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $modal.body.find('.btn').prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            let $row = $('#row'+$element.attr('data-id'));

            $row.html($.sanitize($(response.view).html()));
            $row.addClass('alert-primary');
            setTimeout(function() {
                $row.removeClassStartingWith('alert-');
            }, 5000);
            $('.modal').modal('hide');
        }
    });
});
