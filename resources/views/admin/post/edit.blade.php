<form data-route="{{ route('admin.post.update', ['post' => $post->id]) }}"
data-id="{{ $post->id }}" id="update">
    <div class="form-group">
        <label for="title">{{ trans('icore::posts.title') }}</label>
        <input type="text" value="{{ $post->title }}" name="title"
        class="form-control" id="title">
    </div>
    <div class="form-group">
        <label for="content_html">{{ trans('icore::posts.content') }}</label>
        <div class="@isTheme('dark', 'trumbowyg-dark')">
            <textarea name="content_html" id="trumbowyg" class="form-control"
            rows="10" id="content_html">{{ old('content_html', $post->content_html) }}</textarea>
        </div>
    </div>
    <button type="button" data-id="{{ $post->id }}" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
