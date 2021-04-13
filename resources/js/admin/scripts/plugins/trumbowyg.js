jQuery(document).on('readyAndAjax', function () {
    if (!$('.trumbowyg-box').length) {
        let $trumbowyg = $('#content_html_trumbowyg');

        $trumbowyg.trumbowyg({
            lang: $trumbowyg.data('lang'),
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
                $modal.input.wrap('<div style="position:absolute;top:0;right:0;width:70%;max-width:330px;"><div class="input-group" style="display:flex;">');
                $modal.input.after('<div class="input-group-append" style="order:2;"><button class="btn btn-primary px-2 py-0" type="button" id="filemanager" style="height:27px;"><i class="far fa-image"></i></button></div>');
            }
        });

        $trumbowyg.on('tbwopenfullscreen', function () {
            $('.trumbowyg-fullscreen .trumbowyg-editor').css({
                'cssText': `height: calc(100% - ${$('.trumbowyg-button-pane').height()}px) !important`
            });
        });

        $(document).on('click', 'button#filemanager', function (e) {
            e.preventDefault();

            window.open(
                '/admin/file-manager/fm-button',
                'fm',
                'resizable=yes,status=no,scrollbars=yes,toolbar=no,menubar=no,width=1366,height=768'
            );
        });
    }
});

function fmSetLink($url) {
    $('div.trumbowyg-modal-box').find('input[name=url]').val($url);
}