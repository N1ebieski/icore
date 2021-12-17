$(document).on(
    'click.n1ebieski/icore/web/scripts/view/policy@agree',
    '#policy #agree',
    function (e) {
        e.preventDefault();

        $('#policy').remove();

        $.cookie("policy_agree", 1, { 
            path: '/',
            expires: 365
        });
    }
);
