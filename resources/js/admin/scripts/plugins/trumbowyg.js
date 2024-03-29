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

$(document).on('readyAndAjax.n1ebieski/icore/admin/scripts/plugins/trumbowyg@init', function () {
    if (!$('.trumbowyg-box').length) {
        let $trumbowyg = $('#content_html_trumbowyg');

        $trumbowyg.trumbowyg({
            lang: $trumbowyg.data('lang'),
            fixedBtnPane: $trumbowyg.data('fixed-btn-pane') || true,
            fixedFullWidth: $trumbowyg.data('fixed-full-width') || false,  
            semantic: {
                'b': 'strong',
                'i': 'em',
                's': 'del',
                'strike': 'del',
                'div': 'div'
            },                      
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {
                more: {
                    fn: function () {
                        $trumbowyg.trumbowyg('execCmd', {
                            cmd: 'insertHtml',
                            param: '<p>[more]</p>',
                            forceCss: false,
                        });
                    },
                    title: 'Button "show more"',
                    ico: 'more'
                }
            },
            btns: [
                ['viewHTML'],
                ['historyUndo', 'historyRedo'],
                // ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['foreColor', 'backColor'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['table'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['more'],
                ['fullscreen']
            ]
        });

        $trumbowyg.on('tbwmodalopen', function () {
            let $modal = $('div.trumbowyg-modal-box');
            $modal.input = $modal.find('input[name=url]');

            if (!$modal.input.length) {
                return;
            }

            if ($modal.find('input[name=alt]').length) {
                $modal.input.css({'position': 'initial', 'width': '50px', 'flex': 'auto', 'order': '1'});
                $modal.input.wrap('<div style="max-width:330px;"><div class="input-group" style="display:flex;">');
                $modal.input.after('<div class="input-group-append" style="order:2;"><button class="btn btn-primary px-2 py-0" type="button" id="filemanager" style="height:27px;"><i class="far fa-image"></i></button></div>');
            }
        });

        $trumbowyg.on('tbwopenfullscreen', function () {
            $('.trumbowyg-fullscreen .trumbowyg-editor').css({
                'cssText': `height: calc(100% - ${$('.trumbowyg-button-pane').height()}px) !important`
            });
        });

        $(document).on(
            'click.n1ebieski/icore/admin/scripts/plugins/trumbowyg@fileManager',
            'button#filemanager',
            function (e) {
                e.preventDefault();

                window.open(
                    '/admin/file-manager/fm-button',
                    'fm',
                    'resizable=yes,status=no,scrollbars=yes,toolbar=no,menubar=no,width=1366,height=768'
                );
            }
        );
    }
});

// eslint-disable-next-line
function fmSetLink ($url) {
    $('div.trumbowyg-modal-box').find('input[name=url]').val($url);
}
