@component('icore::admin.partials.modal')

@slot('modal_id', "show-report-{$model->poli_self}-modal")

@slot('modal_title')
<span>{{ trans('icore::reports.route.show') }}</span>
@endslot

@slot('modal_body')
<div>
    @if ($reports->isNotEmpty())
    @foreach ($reports as $report)
    <div id="report{{ $report->id }}" class="transition my-3">
        <div class="d-flex mb-2">
            <small class="mr-auto">
                {{ trans('icore::reports.created_at') }}: {{ $report->created_at_diff }}
            </small>
            @if (!is_null($report->user))
            <small class="ml-auto">
                {{ trans('icore::reports.author') }}: {{ $report->user->name }}
            </small>
            @endif
        </div>
        <div>
            {{ $report->content }}
        </div>
    </div>
    @endforeach
    @endif
</div>
@endslot

@slot('modal_footer')
<div>
    <button 
        type="button" 
        class="btn btn-danger clear-report"
        data-route="{{ route("admin.report.{$model->poli_self}.clear", [$model->id]) }}"
        data-id="{{ $model->id }}"
    >
        <i class="far fa-trash-alt"></i>
        <span>{{ trans('icore::default.clear') }}</span>
    </button>
</div>
@endslot

@endcomponent
