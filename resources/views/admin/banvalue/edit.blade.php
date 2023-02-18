@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-modal')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::bans.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-banvalue"
    method="post" 
    data-route="{{ route('admin.banvalue.update', [$ban->id]) }}" 
    data-id="{{ $ban->id }}"
>
    <div class="form-group">
        <label for="value">
            {{ trans('icore::bans.value.value') }}:
        </label>
        <input 
            class="form-control" 
            type="text" 
            id="value" 
            name="value" 
            value="{{ $ban->value }}"
        >
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update"
        form="edit-banvalue"
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
