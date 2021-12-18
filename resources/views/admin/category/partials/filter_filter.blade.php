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
<div class="form-group">
    <label for="filter-parent">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.parent') }}"
    </label>
    <select 
        id="filter-parent"       
        name="filter[parent]" 
        class="selectpicker select-picker-category" 
        data-live-search="true"
        data-abs="true"
        data-abs-max-options-length="10"
        data-abs-text-attr="name"
        data-abs-ajax-url="{{ route("api.category.{$model->poli}.index") }}"
        data-abs-default-options="{{ json_encode([['value' => '', 'text' => trans('icore::filter.default')], ['value' => 0, 'text' => trans('icore::categories.roots')]]) }}"
        data-style="border"
        data-width="100%"
        data-size="5"
    >
        <optgroup label="{{ trans('icore::default.current_option') }}">
            <option value="">
                {{ trans('icore::filter.default') }}
            </option>
            <option 
                value="0" 
                {{ ($filter['parent'] !== null && $filter['parent'] === 0) ? 'selected' : '' }}
            >
                {{ trans('icore::categories.roots') }}
            </option>
            @if ($filter['parent'] !== null && $filter['parent'] !== 0)
            <option 
                @if ($filter['parent']->ancestors->isNotEmpty())
                data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $filter['parent']->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $filter['parent']->name }}'
                @endif
                value="{{ $filter['parent']->id }}" 
                selected
            >
                {{ $filter['parent']->name }}
            </option>
            @endif
        </optgroup>
    </select>
</div>
</form>
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
