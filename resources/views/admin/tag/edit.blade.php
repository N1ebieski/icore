@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-modal')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::tags.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-tag"
    method="post" 
    data-route="{{ route('admin.tag.update', [$tag->tag_id]) }}" 
    data-id="{{ $tag->tag_id }}"
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::tags.name') }}:
        </label>
        <input
            type="text" 
            value="{{ $tag->name }}" 
            name="name"
            id="name" 
            class="form-control"
        >
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update"
        form="edit-tag"
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