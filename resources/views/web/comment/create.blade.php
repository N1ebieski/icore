<form 
    id="create-comment" 
    class="position-relative"
    data-route="{{ route("web.comment.{$model->poli_self}.store", [$model->id]) }}" 
    method="post"
>
    <input 
        type="hidden" 
        id="parent_id" 
        name="parent_id" 
        value="{{ $parent_id }}"
    >
    <div class="form-group">
        <textarea 
            class="form-control" 
            rows="3" 
            id="content" 
            name="content"
        ></textarea>
    </div>
    @render('icore::captchaComponent', ['id' => $parent_id])
    <button type="button" class="btn btn-primary store-comment">
        {{ trans('icore::default.submit') }}
    </button>
</form>
