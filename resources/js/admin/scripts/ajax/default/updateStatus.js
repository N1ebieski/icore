jQuery(document).on('click', '.status', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            status: $element.attr('data-status'),
        },
        beforeSend: function() {
            $row.find('.btn').prop('disabled', true);
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.html($.sanitize($(response.view).html()));

            if (response.status == 1) {
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }

            if (response.status == 0) {
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        }
    });
});
