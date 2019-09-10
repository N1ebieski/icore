jQuery(document).on('click', 'a.rateComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $ratingComment = $element.closest('[id^=comment]').find('span.rating');

    $.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
        },
        complete: function() {
            $ratingComment.addClass('font-weight-bold');
        },
        success: function(response) {
            $ratingComment.text(response.sum_rating);
        }
    });
});
