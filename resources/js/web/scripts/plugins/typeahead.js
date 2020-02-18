(function($) {
    let typeahead = function() {
        let $input = $("#typeahead");
        let $form = $input.closest('form');

        let engine = new Bloodhound({
            remote: {
                url: $input.attr('data-route')+'?search=%QUERY%',
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('search'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        $input.typeahead({
            hint: true,
            highlight: true,
            minLength: 3
        }, {
            source: engine.ttAdapter(),
            display: function(data) {
                return $($.parseHTML(data.name)).text();
            },
            templates: {
                suggestion: function(data) {
                    let name = $($.parseHTML(data.name)).text();
                    let href = $form.attr('action')+'?source='+$form.find('[name="source"]').val()+'&search='+name;

                    return $.sanitize('<a href="'+href+'" class="list-group-item py-2 text-truncate">'+name+'</a>');
                }
            }
        });
    };

    jQuery(document).ready(function() {
        $.when( typeahead() ).then(function() {
            $("input.tt-input").css('background-color', '');
        });
    });
})(jQuery);
