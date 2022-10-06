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

$(document).on('ready.n1ebieski/icore/web/scripts/plugins/typeahead@init', function () {
    let typeahead = function () {
        let $input = $("#typeahead");

        let $form = $input.closest('form');

        let engine = new Bloodhound({
            remote: {
                url: $input.data('route') + '?filter[search]=%QUERY%',
                filter: function (list) {
                    return $.map(list.data, function (item) { return { name: item.name }; });
                },                               
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        $input.typeahead({
            hint: true,
            highlight: true,
            minLength: 3
        }, {
            limit: 5,            
            source: engine.ttAdapter(),
            display: function (data) {
                return $($.parseHTML(data.name)).text();
            },
            templates: {
                suggestion: function (data) {
                    let name = $($.parseHTML(data.name)).text();
                    let href = $form.attr('action') + '?source=' + $form.find('[name="source"]').val() + '&search=' + name;

                    return $.sanitize('<a href="' + href + '" class="list-group-item py-2 text-truncate">' + name + '</a>');
                }
            }
        });
    };

    $.when( typeahead() ).then(function () {
        $("input.tt-input").css('background-color', '');
    });
});
