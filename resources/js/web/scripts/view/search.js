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
    'click.n1ebieski/icore/web/scripts/view/search@toggle',
    '.search-toggler',
    function (e) {
        e.preventDefault();

        if (window.innerWidth >= 768) {
            $('#pagesToggle, #pages-toggle').fadeToggle(0);
        } else {
            $('#navbarLogo, #navbar-logo').fadeToggle(0);
            $('#navbarToggle, #navbar-toggle').fadeToggle(0);
        }
        $('#searchForm, #search-form').fadeToggle(0);
        $('.search-toggler').find('i').toggleClass("fa-search fa-times");
    }
);

$(document).on('ready.n1ebieski/icore/web/scripts/view/search@disable', function () {
    let $form = $('form#searchForm, form#search-form');
    $form.btn = $form.find('button');

    $form.find('input[name="search"]').keyup(function (e) {
        if ($(this).val().trim().length >= 3) {
            $form.btn.prop('disabled', false);
        } else {
            $form.btn.prop('disabled', true);
        }
    });
});

$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/search@enter', function () {
    let $form = $('form#searchForm, form#search-form');
    $form.btn = $form.find('button');

    $form.find('input[name="search"]').on('keypress', function (e) {
        if (e.which == 13 && $form.btn.prop('disabled') === false) {
            $('form#searchForm, form#search-form').trigger('submit');
            return false;
        }
    });
});
