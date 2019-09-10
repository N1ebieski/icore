<form method="post" data-route="{{ route('admin.comment.update', ['comment' => $comment->id]) }}"
data-id="{{ $comment->id }}">
    <div class="form-group">
        <label for="content">{{ trans('icore::comments.content') }}</label>
        <textarea name="content" class="form-control"
        rows="10" id="content">{{ $comment->content_html }}</textarea>
    </div>
    <button type="button" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
