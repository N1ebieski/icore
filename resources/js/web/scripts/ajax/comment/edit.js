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
    'click.n1ebieski/icore/web/scripts/ajax/comment@edit',
    'a.editComment, a.edit-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $comment = $element.closest('[id^=comment]');

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            beforeSend: function () {
                $comment.children('div').hide();
                $comment.addLoader({
                    type: 'spinner-border',
                    class: 'loader'
                });
            },
            complete: function () {
                $comment.find('.loader').remove();
            },
            success: function (response) {
                $comment.append($.sanitize(response.view));
            },
            error: function (response) {
                $comment.children('div').show();

                if (response.responseJSON.message) {
                    $comment.parent().addAlert(response.responseJSON.message);
                }
            }
        });
    }
);

$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@cancel',
    'button.editCommentCancel, button.edit-comment-cancel',
    function (e) {
        e.preventDefault();

        let $comment = $(this).closest('[id^=comment]');

        $comment.children('div').show();
        $comment.find('form#editComment, form#edit-comment').remove();
    }
);
