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

$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/plugins/bootstrap-select/default@init', function () {
    $('select.select-picker').each(function () {
        let $sp = $(this);

        if ($sp.data('loaded') === true) {
            return;
        }

        $sp.selectpicker().on('changed.bs.select', function () {
            $sp.next('button').find('.filter-option-inner-inner > small').remove();
        }).on('shown.bs.select', function () {
            $sp.parent().find('.dropdown-menu').find('input[type="search"]').attr('name', 'search');
        }).trigger('change');

        if ($sp.data('abs') === true) {
            $sp.ajaxSelectPicker({
                ajax: {
                    data: function () {
                        return {
                            filter: {
                                search: '{{{q}}}',
                                except: $sp.data('abs-filter-except') || null,
                                status: 1
                            }
                        };
                    }
                },
                preprocessData: function (data) {
                    let array = [];
                    let length = $sp.data('abs-max-options-length') || data.data.length;

                    let defaultOptions = $sp.data('abs-default-options') || [];

                    $.each(defaultOptions, function (key, value) {
                        array.push({
                            value: value.value,
                            text: value.text
                        });
                    });

                    $.each(data.data, function (key, value) {
                        if (key >= length) {
                            return false;
                        }

                        array.push({
                            value: $sp.data('abs-value-attr') ? value[$sp.data('abs-value-attr')] : value.id,
                            text: $sp.data('abs-text-attr') ? value[$sp.data('abs-text-attr')] : value.name,
                        });
                    });
        
                    return array;
                },
                minLength: 3,
                preserveSelected: typeof $sp.data('abs-preserve-selected') === "boolean" ? $sp.data('abs-preserve-selected') : true,
                preserveSelectedPosition: $sp.data('abs-preserve-selected-position') || 'before',
                langCode: $sp.data('abs-lang-code') || null
            });

            // Fix Cannot unselect option element when preserveSelected is true #85
            $sp.trigger('change').data('AjaxBootstrapSelect').list.cache = {};
        }

        // Fix temporary for jsvalidation errors placement
        $sp.parent().addClass('input-group');

        $sp.attr('data-loaded', true);
    });
});