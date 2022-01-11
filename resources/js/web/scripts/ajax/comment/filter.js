$(document).on('ready.n1ebieski/icore/web/scripts/ajax/comment@filter', function () {
    let ajaxFilterComment = function ($form, href) {
        $.ajax({
            url: href,
            method: 'get',
            dataType: 'html',
            beforeSend: function () {
                $('#filterContent, #filter-content').find('.btn').prop('disabled', true);
                $('#filterOrderBy, #filter-orderby').prop('disabled', true);
                $('#filterPaginate, #filter-paginate').prop('disabled', true);
                $form.children('div').addLoader();
                $('#filterModal, #filter-modal').modal('hide');
            },
            complete: function () {
                $form.find('.loader-absolute').remove();
                $('div#comment').find('.captcha').recaptcha();
            },
            success: function (response) {
                $('#filterContent, #filter-content').html($.sanitize($(response).find('#filterContent, #filter-content').html()));

                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    };

    $(document).on(
        'change.n1ebieski/icore/web/scripts/ajax/comment@filterOrderBy',
        '#filterCommentOrderBy, #filter-orderby-comment',
        function (e) {
            e.preventDefault();

            let $form = $('#filter');
            $form.href = $form.data('route') + '?' + $form.serialize();

            ajaxFilterComment($form, $form.href);
        }
    );
});
