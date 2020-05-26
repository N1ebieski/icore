@inject('post', 'N1ebieski\ICore\Models\Post')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i> 
<span>{{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="FormSearch">{{ trans('icore::filter.search.label') }}</label>
    <input type="text" class="form-control" id="FormSearch" placeholder="{{ trans('icore::filter.search.placeholder') }}"
    name="filter[search]" value="{{ isset($filter['search']) ? $filter['search'] : '' }}">
</div>
<div class="form-group">
    <label for="FormStatus">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status.label') }}"</label>
    <select class="form-control custom-select" id="FormStatus" name="filter[status]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        <option value="{{ $post::ACTIVE }}" {{ ($filter['status'] === $post::ACTIVE) ? 'selected' : '' }}>
            {{ trans('icore::posts.status.'.$post::ACTIVE) }}
        </option>
        <option value="{{ $post::INACTIVE }}" {{ ($filter['status'] === $post::INACTIVE) ? 'selected' : '' }}>
            {{ trans('icore::posts.status.'.$post::INACTIVE) }}
        </option>
        <option value="{{ $post::SCHEDULED }}" {{ ($filter['status'] === $post::SCHEDULED) ? 'selected' : '' }}>
            {{ trans('icore::posts.status.'.$post::SCHEDULED) }}
        </option>
    </select>
</div>
@if ($categories->count() > 0)
<div class="form-group">
    <label for="category">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.category') }}"</label>
    <select class="form-control custom-select" id="category" name="filter[category]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        @foreach ($categories as $cats)
            @if ($cats->real_depth == 0)
                <optgroup label="----------"></optgroup>
            @endif
        <option value="{{ $cats->id }}" {{ ($filter['category'] !== null && $filter['category']->id == $cats->id) ? 'selected' : '' }}>
            {{ str_repeat('-', $cats->real_depth) }} {{ $cats->name }}
        </option>
        @endforeach
    </select>
</div>
@endif
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Post\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
