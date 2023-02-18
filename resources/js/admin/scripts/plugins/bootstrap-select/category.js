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

$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/plugins/bootstrap-select/category@init', function () {
    $.extend($.fn.ajaxSelectPicker.locale.pl, {
        noTranslation: 'Brak tłumaczenia'
    });

    $.extend($.fn.ajaxSelectPicker.locale.en, {
        noTranslation: 'No translation'
    });

    $('select.select-picker-category').each(function () {
        let $sp = $(this);

        if ($sp.data('loaded') === true) {
            return;
        }

        if ($sp.data('lang') === 'en') {
            $.extend($.fn.selectpicker.defaults, {
                noneSelectedText: 'Nothing selected',
                noneResultsText: 'No results match {0}',
                countSelectedText: function (numSelected, numTotal) {
                    return (numSelected == 1) ? '{0} item selected' : '{0} items selected';
                },
                maxOptionsText: function (numAll, numGroup) {
                    return [
                        (numAll == 1) ? 'Limit reached ({n} item max)' : 'Limit reached ({n} items max)',
                        (numGroup == 1) ? 'Group limit reached ({n} item max)' : 'Group limit reached ({n} items max)'
                    ];
                },
                selectAllText: 'Select All',
                deselectAllText: 'Deselect All',
                multipleSeparator: ', ',
            });
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
                                orderby: 'real_depth|desc',
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
                            data: {
                                content: value.ancestors.length ?
                                    '<small class="p-0 m-0">' + value.ancestors.map(item => item.name || $sp.data('AjaxBootstrapSelect').t('noTranslation')).join(' &raquo; ') + ' &raquo; </small>' + value.name
                                    : null
                            }
                        });
                    });
        
                    return array;
                },
                minLength: 3,
                preserveSelected: typeof $sp.data('abs-preserve-selected') === "boolean" ? $sp.data('abs-preserve-selected') : true,
                preserveSelectedPosition: $sp.data('abs-preserve-selected-position') || 'before',
                langCode: $sp.data('lang') || null
            });

            // Fix Cannot unselect option element when preserveSelected is true #85
            $sp.trigger('change').data('AjaxBootstrapSelect').list.cache = {};            
        }

        // Fix temporary for jsvalidation errors placement
        $sp.parent().addClass('input-group');

        $sp.attr('data-loaded', true);
    });
});
