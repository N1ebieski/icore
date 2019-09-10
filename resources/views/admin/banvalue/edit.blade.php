<form method="post" data-route="{{ route('admin.banvalue.update', [$ban->id]) }}" data-id="{{ $ban->id }}">
    <div class="form-group">
        <label for="value">{{ trans('icore::bans.value.value') }}</label>
        <input class="form-control" type="text" id="value" name="value" value="{{ $ban->value }}">
    </div>
    <button type="button" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
