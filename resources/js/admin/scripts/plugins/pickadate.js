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

$(document).on('ready.n1ebieski/icore/admin/scripts/plugins/pickadate@init', function () {
    if ($('.datepicker, .timepicker').data('lang') === 'pl') {
        $.extend($.fn.pickadate.defaults, {
            monthsFull: ['styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'],
            monthsShort: ['sty', 'lut', 'mar', 'kwi', 'maj', 'cze', 'lip', 'sie', 'wrz', 'paź', 'lis', 'gru'],
            weekdaysFull: ['niedziela', 'poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota'],
            weekdaysShort: ['niedz.', 'pn.', 'wt.', 'śr.', 'cz.', 'pt.', 'sob.'],
            today: 'Dzisiaj',
            clear: 'Usuń',
            close: 'Zamknij',
            firstDay: 1,
            format: 'd mmmm yyyy',
            formatSubmit: 'yyyy/mm/dd'
        });

        $.extend($.fn.pickatime.defaults, {
            clear: 'usunąć'
        });
    }

    $('form#createPost .datepicker, form#editFullPost .datepicker, form#create-post .datepicker, form#editfull-post .datepicker').pickadate({
        clear: '',
        formatSubmit: 'yyyy-m-dd',
        hiddenName: true
    });

    $('form#createMailing .datepicker, form#editMailing .datepicker, form#create-mailing .datepicker, form#edit-mailing .datepicker').pickadate({
        clear: '',
        formatSubmit: 'yyyy-m-dd',
        hiddenName: true,
        min: new Date(),
    });

    $('.timepicker').pickatime({
        clear: '',
        format: 'H:i',
        formatSubmit: 'HH:i',
        hiddenName: true
    });
});