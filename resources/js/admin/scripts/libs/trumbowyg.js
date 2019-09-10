jQuery(document).on('readyAndAjax', function() {
    if (!$('.trumbowyg-box').length) {
        $('#trumbowyg').trumbowyg({
            lang: 'pl',
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {
                more: {
                    fn: function() {
                        $('#trumbowyg').trumbowyg('execCmd', {
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
                ['undo', 'redo'], // Only supported in Blink browsers
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
