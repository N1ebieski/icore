$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@take',
    'a.takeComment, a.take-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $element.closest('[id^=row]');
        let $div = $element.closest('div');

        $.ajax({
            url: $element.data('route'),
            method: 'post',
            data: {
                // Pobieramy IDki wcześniejszych komentarzy i podajemy je do backendu,
                // żeby wykluczył je z paginacji
                filter: {
                    except: $row.children('[id^=row]')
                        .map(function(){
                            return $(this).attr('data-id');
                        })
                        .get(),
                    orderby: $element.closest('#filterContent, #filter-content').find('#filterCommentOrderBy, #filter-orderby-comment').val()
                },
            },
            beforeSend: function () {
                $element.hide();
                $div.append($.getLoader('spinner-border', 'loader'));
            },
            complete: function () {
                $div.find('.loader').remove();
            },
            success: function (response) {
                $row.append($.sanitize(response.view));
            }
        });
    }
);
