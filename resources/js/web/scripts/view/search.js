$(document).on('click', '.search-toggler', function(e) {
    e.preventDefault();

    if (window.innerWidth >= 768) {
        $('#pagesToggle').fadeToggle(0);
    } else {
        $('#navbarLogo').fadeToggle(0);
        $('#navbarToggle').fadeToggle(0);
    }
    $('#searchForm').fadeToggle(0);
    $('.search-toggler').find('i').toggleClass("fa-search fa-times");
});

$(document).ready(function() {
    let $form = $('form#searchForm');
    $form.btn = $form.find('button');

    $form.find('input[name="search"]').keyup(function(e) {
        if ($(this).val().trim().length >= 3) {
            $form.btn.prop('disabled', false);
        } else {
            $form.btn.prop('disabled', true);
        }
    });
});
