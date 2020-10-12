<div>
    @if ($reports->isNotEmpty())
    @foreach ($reports as $report)
    <div id="report{{ $report->id }}" class="transition my-3">
        <div class="d-flex mb-2">
            <small class="mr-auto">
                {{ trans('icore::reports.created_at') }}: {{ $report->created_at_diff }}
            </small>
            <small class="ml-auto">
                {{ trans('icore::reports.created_at') }}: {{ $report->user->name }}
            </small>
        </div>
        <div>
            {{ $report->content }}
        </div>
    </div>
    @endforeach
    @endif
    <button 
        type="button" 
        class="btn btn-danger clearReport"
        data-route="{{ route("admin.report.{$model->poli_self}.clear", [$model->id]) }}"
        data-id="{{ $model->id }}"
    >
        <i class="far fa-trash-alt"></i>
        <span>{{ trans('icore::default.clear') }}</span>
    </button>
</div>
