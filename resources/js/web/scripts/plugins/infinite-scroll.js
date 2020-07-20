//$('ul.pagination').hide();
jQuery(document).on('readyAndAjax', function () {
    let $is = $('#infinite-scroll');

    $is.jscroll({
        debug: false,
        autoTrigger: $is.data('autotrigger') == true ? true : false,
        data: function () {
            let except = $(this).find('[id^=row]').map(function () {
                return $(this).attr('data-id');
            }).get();

            if (except.length) {
                return {
                    except: except
                };
            }
        },
        loadingHtml: $.getLoader('spinner-border', 'loader'),
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
