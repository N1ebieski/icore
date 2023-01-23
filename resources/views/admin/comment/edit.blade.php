@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-comment-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::comments.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-comment"
    method="post" 
    data-route="{{ route('admin.comment.update', ['comment' => $comment->id]) }}"
    data-id="{{ $comment->id }}"
>
    <div class="form-group">
        <label for="content">
            {{ trans('icore::comments.content') }}:
        </label>
        <textarea 
            name="content" 
            class="form-control"
            rows="10" 
            id="content"
        >{{ $comment->content_html }}</textarea>
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update"
        form="edit-comment"
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