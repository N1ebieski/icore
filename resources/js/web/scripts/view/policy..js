jQuery(document).on('click', '#policy #agree', function (e) {
    e.preventDefault();

    $('#policy').remove();

    $.cookie("policyAgree", 1, { 
        path: '/',
        expires: 365
    });
});