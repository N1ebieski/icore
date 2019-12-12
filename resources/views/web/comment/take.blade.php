@foreach ($comments as $comment)
@include('icore::web.comment.partials.comment', ['comment' => $comment,
'post_id' => $parent->model_id])
@endforeach
@if ($comments->currentPage() < $comments->lastPage())
<div>
    <a href="#" data-route="{{ route('web.comment.take', [$parent->id]) }}"
    role="button" class="btn btn-outline-secondary text-nowrap depth-1 takeComment">
        {{ trans('icore::comments.next_comments') }}
        <i class="fas fa-angle-down"></i>
    </a>
</div>
@endif
