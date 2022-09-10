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
    'click.n1ebieski/icore/web/scripts/ajax/comment@take',
    'a.takeComment, a.take-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $element.closest('[id^=row]');
        let $div = $element.closest('div');

        $.ajax({
            url: $element.data('route'),
            method: 'post',
            data: {
                // Pobieramy IDki wcześniejszych komentarzy i podajemy je do backendu,
                // żeby wykluczył je z paginacji
                filter: {
                    except: $row.children('[id^=row]')
                        .map(function (){
                            return $(this).attr('data-id');
                        })
                        .get(),
                    orderby: $element.closest('#filterContent, #filter-content').find('#filterCommentOrderBy, #filter-orderby-comment').val()
                },
            },
            beforeSend: function () {
                $element.hide();
                $div.addLoader({
                    type: 'spinner-border',
                    class: 'loader'
                });
            },
            complete: function () {
                $div.find('.loader').remove();
            },
            success: function (response) {
                $row.append($.sanitize(response.view));
            }
        });
    }
);
