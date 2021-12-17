@component('icore::admin.partials.modal')

@slot('modal_id', 'create-banuser-modal')

@slot('modal_title')
<i class="fas fa-user-slash"></i>
<span> {{ trans('icore::bans.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    method="post"
    id="edit-banmodel"
    data-route="{{ route('admin.banmodel.user.store', [$model->id]) }}"
>
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="user" 
                name="user" 
                value="{{ $model->id }}"
            >
            <label class="custom-control-label" for="user">
                {{ trans('icore::bans.model.user.user') }}: {{ $model->name }}
            </label>
        </div>
    </div>
    @if (!is_null($model->ip))
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="ip" 
                name="ip" 
                value="{{ $model->ip }}"
            >
            <label class="custom-control-label" for="ip">
                {{ trans('icore::bans.value.ip.ip') }}: {{ $model->ip }}
            </label>
        </div>
    </div>
    @endif
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button type="button" class="btn btn-primary store-banmodel">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot

@endcomponent
