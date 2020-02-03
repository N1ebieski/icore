//$('ul.pagination').hide();
jQuery(document).on('readyAndAjax', function() {
    $('#infinite-scroll').jscroll({
        debug: false,
        autoTrigger: false,
        data: function() {
            let except = $(this).find('[id^=row]').map(function() {
                return $(this).attr('data-id');
            }).get();

            if (except.length) {
                return {
                    except: except
                };
            }
        },
        loadingHtml: $.getLoader('spinner-border', 'loader'),
        loadingFunction: function() {
            $('#is-pagination').first().remove();
        },
        padding: 0,
        nextSelector: 'a#is-next:last',
        contentSelector: '#infinite-scroll',
        pagingSelector: '.pagination',
        callback: function(nextHref) {
            let href = nextHref.split(' ')[0];
            let page = $.getUrlParameter(href, 'page');
            let title = $('#is-pagination:last').attr('data-title').replace(/(\d+)/, '').trim();

            if ($.isNumeric(page) && title.length) {
                let regex = new RegExp(title+"\\s(\\d+)");
                document.title = document.title.replace(regex, title + ' ' + page);
                history.replaceState(null, null, href);
            }
        }
    });
});
