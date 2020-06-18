@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [$role->name, trans('icore::roles.route.edit')],
    'desc' => [$role->name, trans('icore::roles.route.edit')],
    'keys' => [$role->name, trans('icore::roles.route.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">{{ trans('icore::roles.route.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::roles.route.edit') }}</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="fas fa-edit"></i>
        <span>{{ trans('icore::roles.route.edit') }}:</span>
    </h1>
    <form class="mb-3" method="post" action="{{ route('admin.role.update', [$role->id]) }}" id="editRole">
        @csrf
        @method('put')
        <div class="row">
            @foreach ($permissions->chunk($col_count) as $chunk)
            <div class="col-lg-3 col-sm-6">
                @foreach ($chunk as $permission)
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="perm[{{ $permission->id }}]" value="">
                        <input type="checkbox" class="custom-control-input" id="perm{{ $permission->id }}"
                        {{ old("perm.{$permission->id}", optional($permission->roles->first())->id) !== null ? 'checked' : '' }}
                        name="perm[{{ $permission->id }}]" value="{{ $permission->name }}">
                        <label class="custom-control-label" for="perm{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
            <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                    <label for="name">{{ trans('icore::roles.name') }}</label>
                    <input type="text" value="{{ old('name', $role->name) }}" name="name"
                    id="name" class="form-control {{ $isValid('name') }}"
                    {{ $role->name === 'user' ? 'disabled' : null }}>
                    @includeWhen($errors->has('name'), 'icore::admin.partials.errors', ['name' => 'name'])
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">{{ trans('icore::default.save') }}</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest', '#editRole'); !!}
@endcomponent
@endpush
