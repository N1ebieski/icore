@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::tags.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-tag"
    method="post" 
    data-route="{{ route('admin.tag.store') }}"
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::tags.name') }}:
        </label>
        <input 
            type="text" 
            value="" 
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
        id="create-tag"
        type="button" 
        class="btn btn-primary store"
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