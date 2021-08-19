$(document).on('click', '.search-toggler', function (e) {
    e.preventDefault();

    if (window.innerWidth >= 768) {
        $('#pagesToggle, #pages-toggle').fadeToggle(0);
    } else {
        $('#navbarLogo, #navbar-logo').fadeToggle(0);
        $('#navbarToggle, #navbar-toggle').fadeToggle(0);
    }
    $('#searchForm, #search-form').fadeToggle(0);
    $('.search-toggler').find('i').toggleClass("fa-search fa-times");
});

$(document).ready(function () {
    let $form = $('form#searchForm, form#search-form');
    $form.btn = $form.find('button');

    $form.find('input[name="search"]').keyup(function(e) {
        if ($(this).val().trim().length >= 3) {
            $form.btn.prop('disabled', false);
        } else {
            $form.btn.prop('disabled', true);
        }
    });
});

jQuery(document).on('readyAndAjax', function () {
    let $form = $('form#searchForm, form#search-form');
    $form.btn = $form.find('button');

    $form.find('input[name="search"]').keypress(function (e) {
        if (e.which == 13 && $form.btn.prop('disabled') === false) {
            $('form#searchForm, form#search-form').submit();
            return false;
        }
    });
});
