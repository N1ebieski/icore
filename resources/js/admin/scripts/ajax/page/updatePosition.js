jQuery(document).on('click', 'button.updatePositionPage', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $form = $element.closest('form');
    let $modal = {
        body: $element.closest('.modal').find('.modal-body'),
        content: $element.closest('.modal').find('.modal-content')
    };

    $.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            position: $form.find('#position').val(),
        },
        beforeSend: function() {
            $modal.body.find('.btn').prop('disabled', true);
            $modal.content.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $('.modal').modal('hide');
            $.each(response.siblings, function(key, value) {
                let $rowSibling = $('#row'+key);
                if ($rowSibling.length) {
                    $rowSibling.find('#position').text(value+1);
                    $rowSibling.addClass('alert-primary');
                    setTimeout(function() {
                        $rowSibling.removeClassStartingWith('alert-');
                    }, 5000);
                }
            });
        }
    });
});
