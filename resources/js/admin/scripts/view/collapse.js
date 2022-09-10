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
    'change.n1ebieski/icore/admin/scripts/view/collapse@publishedAt',
    '[aria-controls="collapse-published-at"]',
    function () {
        if ($(this).val() == 0) $('#collapse-published-at').collapse('hide');
        else $('#collapse-published-at').collapse('show');
    }
);

$(document).on(
    'change.n1ebieski/icore/admin/scripts/view/collapse@activationAt',
    '[aria-controls="collapse-activation-at"]',
    function () {
        if ($(this).val() == 2) $('#collapse-activation-at').collapse('show');
        else $('#collapse-activation-at').collapse('hide');
    }
);