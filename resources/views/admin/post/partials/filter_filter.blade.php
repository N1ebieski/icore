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
            value="{{ Post\Status::ACTIVE }}" 
            {{ ($filter['status'] === Post\Status::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::posts.status.'.Post\Status::ACTIVE) }}
        </option>
        <option 
            value="{{ Post\Status::INACTIVE }}" 
            {{ ($filter['status'] === Post\Status::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::posts.status.'.Post\Status::INACTIVE) }}
        </option>
        <option 
            value="{{ Post\Status::SCHEDULED }}" 
            {{ ($filter['status'] === Post\Status::SCHEDULED) ? 'selected' : '' }}
        >
            {{ trans('icore::posts.status.'.Post\Status::SCHEDULED) }}
        </option>
    </select>
</div>
<div class="form-group">
    <label for="filter-category">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.category') }}"
    </label>
    <select 
        id="filter-category"
        name="filter[category]"
        class="selectpicker select-picker-category" 
        data-live-search="true"
        data-abs="true"
        data-abs-max-options-length="10"
        data-abs-text-attr="name"
        data-abs-ajax-url="{{ route("api.category.post.index") }}"
        data-abs-default-options="{{ json_encode([['value' => '', 'text' => trans('icore::filter.default')]]) }}"
        data-style="border"
        data-width="100%"
        data-container="body"
    >
        <optgroup label="{{ trans('icore::default.current_option') }}">
            <option value="">
                {{ trans('icore::filter.default') }}
            </option>
            @if ($filter['category'] !== null)
            <option 
                @if ($filter['category']->ancestors->isNotEmpty())
                data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $filter['category']->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $filter['category']->name }}'
                @endif
                value="{{ $filter['category']->id }}" 
                selected
            >
                {{ $filter['category']->name }}
            </option>
            @endif
        </optgroup>
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Post\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
