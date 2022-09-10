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

$(document).on('ready.n1ebieski/icore/web/recaptcha_invisible@init', function () {
    $('.g-recaptcha[data-size="invisible"]').each(function (i, e) {
        window.grecaptcha.ready(function () {
            $(e).recaptcha();
        });
    });
});

$(document).on('click.n1ebieski/icore/web/recaptcha_invisible@captcha', 'button', function (e) {
    if ($(this).hasClass('captcha')) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let $form;

        if ($(this).is('[form]')) {
            $form = $(`form#${$(this).attr('form')}`);
        }
        
        if (typeof $form === 'undefined' || !$form.length) {
            $form = $(this).closest('form');
        }

        let valid = $.prototype.valid ? $form.valid() : true;

        if (valid) {
            $(this).removeClass('captcha');

            window.grecaptcha.execute($(this).data('widgetid'));    
        }

        return;
    }
});
