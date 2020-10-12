<form method="post" data-route="{{ route('admin.banmodel.user.store', [$model->id]) }}">
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
    <button type="button" class="btn btn-primary storeBanModel">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
