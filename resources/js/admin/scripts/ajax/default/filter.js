$(document).on('ready.n1ebieski/icore/admin/scripts/ajax/default@filter', function () {
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
                // Trick to prevent open modal during filter content append
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').removeAttr('style');

                $('#filterContent, #filter-content').html($.sanitize($(response).find('#filterContent, #filter-content').html()));

                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    }

    $(document).on(
        'change.n1ebieski/icore/admin/scripts/ajax/default@filterOrderBy',
        '#filterOrderBy, #filter-orderby',
            function (e) {
            e.preventDefault();

            let $form = $('#filter');
            $form.href = $form.data('route') + '?' + $form.serialize();

            ajaxFilter($form, $form.href);
        }
    );

    $(document).on(
        'click.n1ebieski/icore/admin/scripts/ajax/default@filterFilter',
        '#filterFilter, #filter-filter',
        function (e) {
            e.preventDefault();

            let $form = $('#filter');
            $form.href = $form.data('route') + '?' + $form.serialize();

            if ($('#filter').valid()) ajaxFilter($form, $form.href);
        }
    );

    $(document).on(
        'click.n1ebieski/icore/admin/scripts/ajax/default@filterOption',
        'a.filterOption, a.filter-option',
        function (e) {
            e.preventDefault();

            let $form = $('#filter');
            $form.href = $form.data('route') + '?' + $form.find('[name!="' + $.escapeSelector($(this).data('name'))+'"]').serialize();

            ajaxFilter($form, $form.href);
        }
    );

    $(document).on(
        'change.n1ebieski/icore/admin/scripts/ajax/default@filterPaginate',
        '#filterPaginate, #filter-paginate',
        function (e) {
            e.preventDefault();

            let $form = $('#filter');
            $form.href =  $form.data('route') + '?' + $form.serialize();

            ajaxFilter($form, $form.href);
        }
    );
})
