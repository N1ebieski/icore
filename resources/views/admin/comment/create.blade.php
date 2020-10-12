<form 
    method="post" 
    data-route="{{ route("admin.comment.{$model->poli_self}.store", [$model->id]) }}"
    data-id="{{ $parent_id }}"
>
    <input 
        type="hidden" 
        id="parent_id" 
        name="parent_id" 
        value="{{ $parent_id }}"
    >
    <div class="form-group">
        <label for="content">
            {{ trans('icore::comments.content') }}
        </label>
        <textarea 
            name="content" 
            class="form-control"
            rows="10" 
            id="content"
        ></textarea>
    </div>
    <button type="button" class="btn btn-primary storeComment">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
