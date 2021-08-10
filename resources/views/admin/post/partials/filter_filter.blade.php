@inject('post', 'N1ebieski\ICore\Models\Post')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filter-modal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i> 
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
            value="{{ $post::ACTIVE }}" 
            {{ ($filter['status'] === $post::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::posts.status.'.$post::ACTIVE) }}
        </option>
        <option 
            value="{{ $post::INACTIVE }}" 
            {{ ($filter['status'] === $post::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::posts.status.'.$post::INACTIVE) }}
        </option>
        <option 
            value="{{ $post::SCHEDULED }}" 
            {{ ($filter['status'] === $post::SCHEDULED) ? 'selected' : '' }}
        >
            {{ trans('icore::posts.status.'.$post::SCHEDULED) }}
        </option>
    </select>
</div>
<div class="form-group">
    <label for="filter-category">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.category') }}"
    </label>
    <select 
        class="selectpicker select-picker-category" 
        data-live-search="true"
        data-abs="true"
        data-abs-max-options-length="10"
        data-abs-text-attr="name"
        data-abs-ajax-url="{{ route("api.category.post.index") }}"
        data-abs-default-options="{{ json_encode([['value' => '', 'text' => trans('icore::filter.default')]]) }}"
        data-style="border"
        data-width="100%"
        name="filter[category]"
        id="filter-category"
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Post\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
