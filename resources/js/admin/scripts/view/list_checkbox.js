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
    'click.n1ebieski/icore/admin/scripts/view/list_checkbox@selectAll',
    '#selectAll, #select-all',
    function () {
        $('#selectForm .select, #select-form .select').prop('checked', $(this).prop('checked')).trigger('change');
    }
);

$(document).on(
    'change.n1ebieski/icore/admin/scripts/view/list_checkbox@select',
    '#selectForm .select, #select-form .select',
    function () {
        if ($('#selectForm .select:checked, #select-form .select:checked').length > 0) {
            $('.select-action').fadeIn();
        }
        else {
            $('.select-action').fadeOut();
        }
    }
);
