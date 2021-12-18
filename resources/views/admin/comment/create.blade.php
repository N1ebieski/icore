@component('icore::admin.partials.modal')

@slot('modal_id', 'create-comment-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-comment"></i>
<span> {{ trans('icore::comments.route.create') }}</span>
@endslot

@slot('modal_body')
<form
    id="create-comment"
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
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store-comment"
        form="create-comment"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot

@endcomponent
