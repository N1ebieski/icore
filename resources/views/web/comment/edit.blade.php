<form 
    id="editComment" 
    class="position-relative" 
    data-route="{{ route('web.comment.update', [$comment->id]) }}" 
    method="post"
>
    <div class="form-group">
        <textarea 
            class="form-control" 
            rows="3" 
            id="content" 
            name="content"
        >{{ $comment->content_html }}</textarea>
    </div>
    <button type="button" class="btn btn-primary updateComment">
        {{ trans('icore::default.submit') }}
    </button>
    <button type="button" class="btn btn-secondary editCommentCancel">
        {{ trans('icore::default.cancel') }}
    </button>
</form>
