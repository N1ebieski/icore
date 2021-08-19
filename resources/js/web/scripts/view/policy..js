jQuery(document).on('click', '#policy #agree', function (e) {
    e.preventDefault();

    $('#policy').remove();

    $.cookie("policy_agree", 1, { 
        path: '/',
        expires: 365
    });
});
