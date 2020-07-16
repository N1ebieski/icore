(function($) {
    $.fn.removeClassStartingWith = function(begin) {
        this.removeClass(function(index, className) {
            return (className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []).join(' ');
        });
    };

    $.fn.focusWithoutScrolling = function() {
        var x = window.scrollX, y = window.scrollY;
        this.focus();
        window.scrollTo(x, y);
    };

    $.sanitize = function(html) {
        let $output = $($.parseHTML('<div>' + html + '</div>', null, false));

        $output.find('*').each(function(index, node) {
            $.each(node.attributes, function() {
                let attrName = this.name;
                let attrValue = this.value;

                if (attrName.indexOf('on') == 0 || attrValue.indexOf('javascript:') == 0) {
                    $(node).removeAttr(attrName);
                }
            });
        });

        return $output.html();
    };

    $.getUrlParameter = function(url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
    };

    $.getLoader = function(type, loader = 'loader-absolute') {
        return '<div class="'+loader+'"><div class="'+type+'"><span class="sr-only">Loading...</span></div></div>';
    };

    $.getAlert = function(response, type) {
        return $.sanitize('<div class="alert alert-'+type+' alert-time" role="alert">'+response+'</div>');
    };

    $.getError = function(key, value) {
        return $.sanitize('<span class="invalid-feedback d-block font-weight-bold" id="error-'+ key+'">'+value+'</span>');
    };
})(jQuery);
