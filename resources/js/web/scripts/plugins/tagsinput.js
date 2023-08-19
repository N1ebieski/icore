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

$(document).on('ready.n1ebieski/icore/web/scripts/plugins/tagsinput@init', function () {
    $('.tagsinput').each(function () {
        let $tagsinput = $(this);

        $tagsinput.tagsInput({
            placeholder: $tagsinput.attr('placeholder'),
            minChars: 3,
            maxChars: $tagsinput.data('max-chars') || 30,
            limit: $tagsinput.data('max'),
            validationPattern: new RegExp('^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\u00E0-\u00FC -]+$)$'),
            unique: true,
        });
    });
});
