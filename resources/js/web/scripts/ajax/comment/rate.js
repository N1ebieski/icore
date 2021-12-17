$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@rate',
    'a.rateComment, a.rate-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $ratingComment = $element.closest('[id^=comment]').find('span.rating');

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            complete: function () {
                $ratingComment.addClass('font-weight-bold');
            },
            success: function (response) {
                $ratingComment.text(response.sum_rating);
            }
        });
    }
);
