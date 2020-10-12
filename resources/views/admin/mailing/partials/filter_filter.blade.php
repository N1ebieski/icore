@inject('mailing', 'N1ebieski\ICore\Models\Mailing')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i> 
<span>{{ trans('icore::filter.filter_title') }}</span>
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
            value="{{ $mailing::ACTIVE }}" 
            {{ ($filter['status'] === $mailing::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.$mailing::ACTIVE) }}
        </option>
        <option 
            value="{{ $mailing::INACTIVE }}" 
            {{ ($filter['status'] === $mailing::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.$mailing::INACTIVE) }}
        </option>
        <option 
            value="{{ $mailing::SCHEDULED }}" 
            {{ ($filter['status'] === $mailing::SCHEDULED) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.$mailing::SCHEDULED) }}
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Mailing\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
