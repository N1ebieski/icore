(function($) {
    let ajaxFilterComment = function($form, href) {
        $.ajax({
            url: href,
            method: 'get',
            dataType: 'html',
            beforeSend: function() {
                $('#filterContent').find('.btn').prop('disabled', true);
                $('#filterOrderBy').prop('disabled', true);
                $('#filterPaginate').prop('disabled', true);
                $form.children('div').append($.getLoader('spinner-border'));
                $('#filterModal').modal('hide');
            },
            complete: function() {
                $form.find('div.loader-absolute').remove();
                $('div#comment').find('.captcha').recaptcha();
            },
            success: function(response) {
                $('#filterContent').html($.sanitize($(response).find('#filterContent').html()));
                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    };

    jQuery(document).on('change', '#filterCommentOrderBy', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilterComment($form, $form.href);
    });
})(jQuery);
