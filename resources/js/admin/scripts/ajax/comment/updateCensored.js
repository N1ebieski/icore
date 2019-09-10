jQuery(document).on('click', 'button.censoreComment', function(e) {
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
            censored: $element.attr('data-censored'),
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

            if (response.censored == 1) {
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }

            if (response.censored == 0) {
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        }
    });
});
