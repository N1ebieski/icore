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
    'click.n1ebieski/icore/web/scripts/ajax/comment@create',
    'a.createComment, a.create-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        
        let $form = $element.closest('[id^=comment]').find('form#createComment, form#create-comment');

        if ($form.length > 0) {
            $form.fadeToggle();
        } else {
            let $comment = $element.closest('[id^=comment]');

            $.ajax({
                url: $element.data('route'),
                method: 'get',
                beforeSend: function () {
                    $element.prop('disabled', true);
                    $comment.addLoader({
                        type: 'spinner-border',
                        class: 'loader'
                    });
                },
                complete: function () {
                    $element.prop('disabled', false);
                    $comment.find('.loader').remove();
                    $comment.find('.captcha').recaptcha();
                },
                success: function (response) {
                    $comment.children('div').append($.sanitize(response.view));
                },
                error: function (response) {
                    if (response.responseJSON.message) {
                        $comment.parent().addAlert(response.responseJSON.message);
                    }
                }
            });
        }
    }
);
