$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/view/clipboard@init', function () {
    $(".clipboard").each(function () {
        $(this).append($.sanitize(`
            <button
                style="position:absolute;right:0;top:0;" 
                class="btn p-0 m-0 copy-to-clipboard" 
                type="submit"
            >
                <i class="fas fa-copy"></i>
            </button>                
        `));
    });
});

$(document).on(
    'click.n1ebieski/icore/web/scripts/view/clipboard@copyToClipboard', 
    '.copy-to-clipboard', 
    function (e) {
        e.preventDefault();

        navigator.clipboard.writeText($(this).parent().text().trim());

        $('body').addToast({
            title: 'Skopiopwano do schowka',
            type: 'success'
        });  
        
        $('.toast').toast('show');
    }
);