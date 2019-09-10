jQuery(document).on('click', 'a.destroyComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.fadeOut('slow');
            $.each(response.descendants, function(key, value) {
                let $rowDescendant = $('#row'+value);
                if ($rowDescendant.length) {
                    $rowDescendant.fadeOut('slow');
                }
            });
        }
    });
});
