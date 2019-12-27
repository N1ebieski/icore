jQuery(document).on('click', 'a.takeComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');
    let $div = $element.closest('div');

    $.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        data: {
            // Pobieramy IDki wcześniejszych komentarzy i podajemy je do backendu,
            // żeby wykluczył je z paginacji
            except: $row.children('[id^=row]').map(function(){
                return $(this).attr('data-id');
            }).get(),
            orderby: $element.closest('#filterContent').find('#filterCommentOrderBy').val()
        },
        beforeSend: function() {
            $element.hide();
            $div.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function() {
            $div.find('div.loader').remove();
        },
        success: function(response) {
            $row.append($.sanitize(response.view));
        }
    });
});
