(function ($) {
    $.fn.chart = function (options) {
        return new Chart(this, options);
    };

    $.fn.serializeObject = function () {

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function (base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function (key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function (){

            // Skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // Adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // Push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // Fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // Named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };

    $.fn.autoHeight = function (options) {
        let options_ = {
            autogrow: typeof options.autogrow === "boolean" ? options.autogrow : true
        };

        function autoHeight_ (element) {
            if (element.offsetHeight < element.scrollHeight) {
                return $(element).css({ 'height': 'auto' })
                    .height(element.scrollHeight);
            }

            return $(element);
        }

        if (options_.autogrow === false) {
            return;
        }

        return this.each(function () {
            autoHeight_(this).on('input', function () {
                autoHeight_(this);
            });
        });
    };

    $.fn.removeClassStartingWith = function (begin) {
        this.removeClass(function (index, className) {
            return (className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []).join(' ');
        });
    };

    $.fn.focusWithoutScrolling = function () {
        var x = window.scrollX, y = window.scrollY;
        this.focus();
        window.scrollTo(x, y);
    };

    $.sanitize = function (html) {
        let $output = $($.parseHTML('<div>' + html + '</div>', null, false));

        $output.find('*').each(function (index, node) {
            $.each(node.attributes, function () {
                let attrName = this.name;
                let attrValue = this.value;

                if (attrName.indexOf('on') == 0 || attrValue.indexOf('javascript:') == 0) {
                    $(node).removeAttr(attrName);
                }
            });
        });

        return $output.html();
    };

    $.getUrlParameter = function (url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
    };

    $.fn.loader = function (action, type = 'spinner-border') {
        if (action == 'show') {
            $(this).parent().find('button').prop('disabled', true);
            $(this).find('i').hide();

            let color = $(this).is('[class*="btn-outline-"]') && (typeof $.cookie('theme_toggle') === 'undefined' || $.cookie('theme_toggle') === 'light') ?
                'text-dark' : 'text-light';

            $(this).prepend($.sanitize('<span class="' + type + ' ' + type + '-sm ' + color + '" role="status" aria-hidden="true"></span>'));
        }

        if (action == 'hide') {
            $(this).parent().find('button').prop('disabled', false);
            $(this).find('i').show();
            $(this).find('[role="status"]').remove();
        }
    };

    $.fn.addLoader = function (options = null) {
        options = {
            type: typeof options === 'string' ? options : 'spinner-border',
            class: options?.class || 'loader-absolute'
        };

        return this.append($.sanitize(`
            <div class="${options.class}">
                <div class="${options.type}">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `));
    };

    $.fn.addAlert = function (options) {
        options = {
            message: typeof options === 'string' ? options : options.message,
            type: options.type || 'danger'
        };

        return this.prepend($.sanitize(`
            <div class="alert alert-${options.type} alert-time" role="alert">
                <button 
                    type="button" 
                    class="text-dark close" 
                    data-dismiss="alert" 
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
                ${options.message}
            </div>
        `));
    };

    $.fn.addToast = function (options) {
        options = {
            title: typeof options === 'string' ? options : options.title,
            type: options.type || 'success',
            message: options.message || ''
        };

        let $toast = $($.sanitize(`
            <div>
                <div 
                    class="toast bg-${options.type}"
                    role="alert" 
                    aria-live="assertive" 
                    aria-atomic="true" 
                    data-delay="20000" 
                >
                    <div class="toast-header">
                        <strong class="mr-auto">${options.title}</strong>
                        <button 
                            type="button" 
                            class="text-dark ml-2 mb-1 close" 
                            data-dismiss="toast" 
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>             
                </div>
            </div>
        `));

        if (options.message.length) {
            $toast.find('.toast').append($.sanitize(`
                <div class="toast-body bg-light text-dark">
                    ${options.message}
                </div>
            `));
        }

        return this.append($toast.html());
    };

    $.fn.addError = function (options) {
        options = {
            message: typeof options === 'string' ? options : options.message,
            id: options.id || null
        };

        let $error = $($.sanitize(`
            <div>
                <span class="invalid-feedback d-block font-weight-bold">${options.message}</span>
            </div>
        `));

        if (options.id !== null) {
            $error.find('.invalid-feedback').attr('id', options.id);
        }

        return this.append($error.html());
    };

    $.fn.clipboard = function (options) {
        if (options.lang === "pl") {
            $.fn.clipboard.defaults.translations = {
                title: "Skopiuj do schowka",
                success: "Skopiowano do schowka",
            };
        }

        options = $.extend({
            lang: "en",
        }, $.fn.clipboard.defaults, options);

        this.append($.sanitize(`
            <button
                style="position:absolute;right:0;top:0;"
                class="btn p-0 m-0 copy-to-clipboard" 
                type="submit"
            >
                <i 
                    data-toggle="tooltip" 
                    data-placement="top"
                    title="${options.translations.title}" 
                    class="fas fa-copy"
                ></i>
            </button>                
        `));

        $(document).trigger('readyAndAjax.*/bootstrap_tooltips@init');

        this.on("click", ".copy-to-clipboard", function (e) {
            e.preventDefault();

            navigator.clipboard.writeText($(this).parent().text().trim());

            $("body").addToast({
                title: options.translations.success,
                type: "success",
            });

            $(document).trigger('readyAndAjax.*/toasts@init');
        });

        return this;
    };
    $.fn.clipboard.defaults = {
        translations: {
            title: "Copy to clipboard",
            success: "Copied to the clipboard",
        },
    };    
})(jQuery);
