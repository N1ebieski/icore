jQuery(document).on('click', 'a.resetMailing', function(e) {
    e.preventDefault();

    var $element = $(this);
    var $row = $('#row'+$element.attr('data-id'));

    $.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.btn').prop('disabled', true);
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.html($.sanitize($(response.view).html()));

            $row.addClass('alert-danger');
            setTimeout(function() {
                $row.removeClassStartingWith('alert-');
            }, 5000);
        }
    });
});
