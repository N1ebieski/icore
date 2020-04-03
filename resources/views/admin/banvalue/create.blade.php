<form method="post" data-route="{{ route('admin.banvalue.store', [$type]) }}">
    <div class="form-group">
        <label for="value">{{ trans('icore::bans.value.value') }}</label>
        <input class="form-control" type="text" id="value" name="value" value="">
    </div>
    <button type="button" class="btn btn-primary store">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
