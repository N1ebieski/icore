@inject('comment', 'N1ebieski\ICore\Models\Comment\Comment')
@inject('report', 'N1ebieski\ICore\Models\Report\Comment\Report')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="FormSearch">
        {{ trans('icore::filter.search.label') }}
    </label>
    <input 
        type="text" 
        class="form-control" 
        id="FormSearch" 
        placeholder="{{ trans('icore::filter.search.placeholder') }}"
        name="filter[search]" 
        value="{{ isset($filter['search']) ? $filter['search'] : '' }}"
    >
</div>
<div class="form-group">
    <label for="FormStatus">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="FormStatus" 
        name="filter[status]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ $comment::ACTIVE }}" 
            {{ ($filter['status'] === $comment::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.status.'.$comment::ACTIVE) }}
        </option>
        <option 
            value="{{ $comment::INACTIVE }}" 
            {{ ($filter['status'] === $comment::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.status.'.$comment::INACTIVE) }}
        </option>
    </select>
</div>
<div class="form-group">
    <label for="FormCensored">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.censored.label') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="FormCensored" 
        name="filter[censored]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ $comment::CENSORED }}" 
            {{ ($filter['censored'] === $comment::CENSORED) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.censored.'.$comment::CENSORED) }}
        </option>
        <option 
            value="{{ $comment::UNCENSORED }}" 
            {{ ($filter['censored'] === $comment::UNCENSORED) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.censored.'.$comment::UNCENSORED) }}
        </option>
    </select>
</div>
<div class="form-group">
    <label for="FormReport">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.report.label') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="FormReport" 
        name="filter[report]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ $report::REPORTED }}" 
            {{ ($filter['report'] === $report::REPORTED) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.report.'.$report::REPORTED) }}
        </option>
        <option 
            value="{{ $report::UNREPORTED }}" 
            {{ ($filter['report'] === $report::UNREPORTED) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.report.'.$report::UNREPORTED) }}
        </option>
    </select>
</div>
<div class="d-inline">
    <button type="button" class="btn btn-primary btn-send" id="filterFilter">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.apply') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot
@endcomponent

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
