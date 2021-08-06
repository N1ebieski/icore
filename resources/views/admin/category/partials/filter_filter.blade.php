@inject('category', 'N1ebieski\ICore\Models\Category\Category')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::filter.filter_title') }}</span>
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
            value="{{ $category::ACTIVE }}" 
            {{ ($filter['status'] === $category::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.active') }}
        </option>
        <option 
            value="{{ $category::INACTIVE }}" 
            {{ ($filter['status'] === $category::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.inactive') }}
        </option>
    </select>
</div>
@if ($parents->count() > 0)
<div class="form-group">
    <label for="filter-parent">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.parent') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-parent" 
        name="filter[parent]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        <optgroup label="----------"></optgroup>
        <option 
            value="0" 
            {{ ($filter['parent'] !== null && $filter['parent'] === 0) ? 'selected' : '' }}
        >
            {{ trans('icore::categories.roots') }}
        </option>
        @foreach ($parents as $parent)
            @if ($parent->real_depth == 0)
                <optgroup label="----------"></optgroup>
            @endif
            <option 
                value="{{ $parent->id }}" 
                {{ (optional($filter['parent'])->id == $parent->id) ? 'selected' : '' }}
            >
                {{ str_repeat('-', $parent->real_depth) }} {{ $parent->name }}
            </option>
        @endforeach
    </select>
</div>
@endif
<div class="d-inline">
    <button type="button" class="btn btn-primary btn-send" id="filter-filter">
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
