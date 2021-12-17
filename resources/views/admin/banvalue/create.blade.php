@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::bans.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-banvalue"
    method="post" 
    data-route="{{ route('admin.banvalue.store', [$type]) }}"
>
    <div class="form-group">
        <label for="value">
            {{ trans('icore::bans.value.value') }}
        </label>
        <input class="form-control" type="text" id="value" name="value" value="">
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store" 
        form="create-banvalue"
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