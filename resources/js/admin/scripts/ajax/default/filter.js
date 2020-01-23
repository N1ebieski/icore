(function($) {
    function ajaxFilter($form, href) {
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
            },
            success: function(response) {
                $('#filterContent').html($.sanitize($(response).find('#filterContent').html()));
                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    }

    jQuery(document).on('change', '#filterOrderBy', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('click', '#filterFilter', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.serialize();

        if (jQuery_2_1_3('#filter').valid()) ajaxFilter($form, $form.href);
    });

    jQuery(document).on('click', '.filterOption', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.find('[name!="'+$.escapeSelector($(this).attr('data-name'))+'"]').serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('change', '#filterPaginate', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href =  $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilter($form, $form.href);
    });
})(jQuery);
