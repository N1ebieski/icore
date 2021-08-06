@inject('user', 'N1ebieski\ICore\Models\User')

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
            value="{{ $user::ACTIVE }}" 
            {{ ($filter['status'] === $user::ACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.active') }}
        </option>
        <option 
            value="{{ $user::INACTIVE }}" 
            {{ ($filter['status'] === $user::INACTIVE) ? 'selected' : '' }}
        >
            {{ trans('icore::filter.inactive') }}
        </option>
    </select>
</div>
<div class="form-group">
    <label for="filter-role">
        {{ trans('icore::filter.filter') }} "{{ trans('icore::filter.role') }}"
    </label>
    <select 
        class="form-control custom-select" 
        id="filter-role" 
        name="filter[role]"
    >
        <option value="">
            {{ trans('icore::filter.default') }}
        </option>
        @foreach ($roles as $role)
        <option 
            value="{{ $role->id }}" 
            {{ ($filter['role'] !== null && $filter['role']->id == $role->id) ? 'selected' : '' }}
        >
            {{ $role->name }}
        </option>
        @endforeach
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
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\User\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
