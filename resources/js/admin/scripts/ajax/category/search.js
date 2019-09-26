function categorySelect()
{
    return $('#categoryOptions .form-group').map(function() {
       return '#' + $(this).attr('id');
    }).get();
}

jQuery(document).on('click', '#searchCategory .btn', function(e) {
    e.preventDefault();

    let $searchCategory = $('#searchCategory');
    $searchCategory.url = $searchCategory.attr('data-route');
    $searchCategory.btn = $searchCategory.find('.btn');
    $searchCategory.input = {
        val: $('#searchCategory input').val()
    };

    $.ajax({
        url: $searchCategory.url+'?name='+$searchCategory.input.val,
        method: 'get',
        dataType: 'json',
        beforeSend: function() {
            $searchCategory.btn.prop('disabled', true);
            $('#searchCategoryOptions').empty();
            $searchCategory.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $searchCategory.btn.prop('disabled', false);
            $searchCategory.find('div.loader-absolute').remove();
        },
        success: function(response) {
            let $response = $(response.view).find(categorySelect().join(',')).remove().end();

            $searchCategory.find('#searchCategoryOptions').html($.sanitize($response.html()));
        }
    });
});

jQuery(document).on('change', '.categoryOption', function() {

    let $searchCategory = $('#searchCategory');
    $searchCategory.max = $searchCategory.attr('data-max');
    let $input = $(this).closest('.form-group');

    if ($(this).prop('checked') == true) {
        $input.appendTo('#categoryOptions');
    } else {
        $input.remove();
    }

    if ($searchCategory.is(':visible') && categorySelect().length >= $searchCategory.max) {
        $searchCategory.fadeOut();
    }

    if (!$searchCategory.is(':visible') && categorySelect().length < $searchCategory.max) {
        $searchCategory.fadeIn();
    }
});

$('#searchCategory input').keypress(function(e) {
    if (e.which == 13) {
        $('#searchCategory .btn').trigger('click');
        return false;
    }
});
