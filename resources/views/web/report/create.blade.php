<form method="post" id="createReport" data-id="{{ $model->id }}"
data-route="{{ route("web.report.{$model->poli_self}.store", [$model->id]) }}">
    <div class="form-group">
        <label for="content">{{ trans('icore::reports.reason') }}</label>
        <input type="text" value="" name="content" class="form-control" id="content">
    </div>
    <button type="button" class="btn btn-primary storeReport">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.submit') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
