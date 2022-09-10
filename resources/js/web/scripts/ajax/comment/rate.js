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

$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@rate',
    'a.rateComment, a.rate-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $ratingComment = $element.closest('[id^=comment]').find('span.rating');

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            complete: function () {
                $ratingComment.addClass('font-weight-bold');
            },
            success: function (response) {
                $ratingComment.text(response.sum_rating);
            }
        });
    }
);
