jQuery(document).on('readyAndAjax', function() {
    if (!$('.trumbowyg-box').length) {
        $('#content_html_trumbowyg').trumbowyg({
            lang: 'pl',
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {
                more: {
                    fn: function() {
                        $('#content_html_trumbowyg').trumbowyg('execCmd', {
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
    }
});
