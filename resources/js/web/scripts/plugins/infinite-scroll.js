/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/plugins/infinite-scroll@init', function () {
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
