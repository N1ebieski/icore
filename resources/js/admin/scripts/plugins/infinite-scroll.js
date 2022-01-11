$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/plugins/infinite-scroll@init', function () {
    let $is = $('#infinite-scroll');

    $is.jscroll({
        debug: false,
        autoTrigger: $is.data('autotrigger') == true ? true : false,
        data: function () {
            let filter = $('#filter').serializeObject().filter || {};

            filter.except = $(this).find('[id^=row]').map(function () {
                return $(this).attr('data-id');
            }).get();

            if (filter.except.length) {
                return {
                    filter: filter
                };
            }
        },
        loadingHtml: '<div class="loader"><div class="spinner-border"><span class="sr-only">Loading...</span></div></div>',
        loadingFunction: function () {
            $('#is-pagination').first().remove();
        },
        padding: 0,
        nextSelector: 'a#is-next:last',
        contentSelector: '#infinite-scroll',
        pagingSelector: '.pagination',
        callback: function (nextHref) {
            let href = nextHref.split(' ')[0];
            history.replaceState(null, null, href);

            // let page = $.getUrlParameter(href, 'page');
            // let title = $('#is-pagination:last').attr('data-title').replace(/(\d+)/, '').trim();

            // if ($.isNumeric(page) && title.length) {
            //     let regex = new RegExp(title+"\\s(\\d+)");
            //     document.title = document.title.replace(regex, title + ' ' + page);
            //     history.replaceState(null, null, href);
            // }
        }
    });
});
