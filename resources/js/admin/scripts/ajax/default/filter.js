(function ($) {
    function ajaxFilter ($form, href) {
        $.ajax({
            url: href,
            method: 'get',
            dataType: 'html',
            beforeSend: function () {
                $('#filterContent, #filter-content').find('.btn').prop('disabled', true);
                $('#filterOrderBy, #filter-orderby').prop('disabled', true);
                $('#filterPaginate, #filter-paginate').prop('disabled', true);

                $form.children('div').append($.getLoader('spinner-border'));

                $('#filterModal, #filter-modal').modal('hide');
            },
            complete: function () {
                $form.find('.loader-absolute').remove();
            },
            success: function (response) {
                $('#filterContent, #filter-content').html($.sanitize($(response).find('#filterContent, #filter-content').html()));

                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    }

    jQuery(document).on('change', '#filterOrderBy, #filter-orderby', function (e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.data('route') + '?' + $form.serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('click', '#filterFilter, #filter-filter', function (e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.data('route') + '?' + $form.serialize();

        if ($('#filter').valid()) ajaxFilter($form, $form.href);
    });

    jQuery(document).on('click', 'a.filterOption, a.filter-option', function (e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.data('route') + '?' + $form.find('[name!="' + $.escapeSelector($(this).data('name'))+'"]').serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('change', '#filterPaginate, #filter-paginate', function (e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href =  $form.data('route') + '?' + $form.serialize();

        ajaxFilter($form, $form.href);
    });
})(jQuery);
