@component('icore::admin.partials.modal')

@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-filter"></i> 
<span> {{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="filter-search">
        {{ trans('icore::filter.search.label') }}:
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
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}":
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
            value="{{ Page\Status::ACTIVE }}" 
            {{ ($filter['status'] === Page\Status::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.active') }}
        </option>
        <option 
            value="{{ Page\Status::INACTIVE }}" 
            {{ ($filter['status'] === Page\Status::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.inactive') }}
        </option>
    </select>
</div>
@if ($parents->count() > 0)
<div class="form-group">
    <label for="filter-parent">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.parent') }}":
    </label>
    <select 
        id="filter-parent"  
        name="filter[parent]"  
        class="selectpicker select-picker" 
        data-live-search="true"
        data-style="border"
        data-width="100%"
        data-container="body"
        data-lang="{{ config('app.locale') }}"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <option 
            value="0" 
            {{ ($filter['parent'] !== null && $filter['parent'] === 0) ? 'selected' : '' }}
        >
            {{ trans('icore::pages.roots') }}
        </option>
        @foreach ($parents as $parent)
        <option 
            @if ($parent->ancestors->isNotEmpty())
            data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $parent->ancestors->pluck('title')->toArray()) }} &raquo; </small>{{ $parent->title }}'
            @endif                        
            value="{{ $parent->id }}" 
            {{ (optional($filter['parent'])->id == $parent->id) ? 'selected' : '' }}
        >
            {{ $parent->title }}
        </option>
        @endforeach
    </select>
</div>
@endif
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Page\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
