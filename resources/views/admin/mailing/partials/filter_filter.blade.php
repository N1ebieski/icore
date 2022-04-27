@component('icore::admin.partials.modal')

@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-filter"></i> 
<span>{{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="filter-search">
        {{ trans('icore::filter.search.label') }}
    </label>
    <input 
        type="text" 
        class="form-control" 
        id="filter-search" 
        placeholder="{{ trans('icore::filter.search.placeholder') }}"
        name="filter[search]" 
        value="{{ isset($filter['search']) ? $filter['search'] : '' }}"
    >
</div>
<div class="form-group">
    <label for="filter-status">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-status" 
        name="filter[status]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="{{ Mailing\Status::ACTIVE }}" 
            {{ ($filter['status'] === Mailing\Status::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.Mailing\Status::ACTIVE) }}
        </option>
        <option 
            value="{{ Mailing\Status::INACTIVE }}" 
            {{ ($filter['status'] === Mailing\Status::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.Mailing\Status::INACTIVE) }}
        </option>
        <option 
            value="{{ Mailing\Status::SCHEDULED }}" 
            {{ ($filter['status'] === Mailing\Status::SCHEDULED) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.Mailing\Status::SCHEDULED) }}
        </option>
        <option 
            value="{{ Mailing\Status::INPROGRESS }}" 
            {{ ($filter['status'] === Mailing\Status::INPROGRESS) ? 'selected' : '' }}
        >
            {{ trans('icore::mailings.status.'.Mailing\Status::INPROGRESS) }}
        </option>        
    </select>
</div>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        id="filter-filter"
        type="button" 
        class="btn btn-primary btn-send"
        form="filter"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.apply') }}</span>
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

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Mailing\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
