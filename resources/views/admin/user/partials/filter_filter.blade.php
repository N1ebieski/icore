@inject('user', 'N1ebieski\ICore\Models\User')

@component('icore::admin.partials.modal')
@slot('modal_id', 'filterModal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span>{{ trans('icore::filter.filter_title') }}</span>
@endslot

@slot('modal_body')
<div class="form-group">
    <label for="FormSearch">{{ trans('icore::filter.search') }}</label>
    <input type="text" class="form-control" id="FormSearch" placeholder="{{ trans('icore::filter.search_placeholder') }}"
    name="filter[search]" value="{{ isset($filter['search']) ? $filter['search'] : '' }}">
</div>
<div class="form-group">
    <label for="FormStatus">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.status') }}"</label>
    <select class="form-control custom-select" id="FormStatus" name="filter[status]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        <option value="{{ $user::ACTIVE }}" {{ ($filter['status'] === $user::ACTIVE) ? 'selected' : '' }}>
            {{ trans('icore::filter.active') }}
        </option>
        <option value="{{ $user::INACTIVE }}" {{ ($filter['status'] === $user::INACTIVE) ? 'selected' : '' }}>
            {{ trans('icore::filter.inactive') }}
        </option>
    </select>
</div>
<div class="form-group">
    <label for="FormRole">{{ trans('icore::filter.filter') }} "{{ trans('icore::filter.role') }}"</label>
    <select class="form-control custom-select" id="FormRole" name="filter[role]">
        <option value="">{{ trans('icore::filter.default') }}</option>
        @foreach ($roles as $role)
        <option value="{{ $role->id }}" {{ ($filter['role'] !== null && $filter['role']->id == $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
        @endforeach
    </select>
</div>
<button type="button" class="btn btn-primary btn-send" id="filterFilter">
    <i class="fas fa-check"></i>
    {{ trans('icore::default.apply') }}
</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">
    <i class="fas fa-ban"></i>
    {{ trans('icore::default.cancel') }}
</button>
@endslot
@endcomponent

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\User\IndexRequest', '#filter'); !!}
@endcomponent
@endpush
