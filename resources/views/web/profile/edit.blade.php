@extends(config('icore.layout') . '::web.profile.layouts.layout', [
    'title' => [trans('icore::profile.page.edit')],
    'desc' => [trans('icore::profile.page.edit')],
    'keys' => [trans('icore::profile.page.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('icore::profile.page.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::profile.page.edit') }}</li>
@endsection

@section('content')
<h1 class="h4 mb-4 border-bottom pb-2"><i class="fas fa-fw fa-user-edit"></i> {{ trans('icore::profile.page.edit') }}:</h1>
<form class="mb-3" method="post" action="{{ route('web.profile.update') }}" id="updateProfile">
    @csrf
    @method('put')
    <div class="form-group row">
        <label for="name" class="col-lg-3 col-form-label text-lg-left">{{ trans('icore::auth.name') }}</label>
        <div class="col-lg-6">
            <input id="name" type="text" class="form-control @isValid('name')" name="name" value="{{ old('name', $user->name) }}" required autofocus>

            @includeWhen($errors->has('name'), 'icore::web.partials.errors', ['name' => 'name'])
        </div>
    </div>
    <div class="form-group row mb-0">
        <div class="col-lg-6 offset-lg-3">
            <button type="submit" class="btn btn-primary">
                {{ trans('icore::default.submit') }}
            </button>
        </div>
    </div>
</form>
<div class="form-group row">
    <label class="col-lg-3 col-form-label text-lg-left">{{ trans('icore::profile.change_password') }}</label>
    <div class="col-lg-6">
        <a href="{{ route('web.profile.redirect_password') }}" class="btn btn-outline-primary"  data-toggle="confirmation"
        type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
        data-btn-ok-class="btn-primary btn-popover" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
        data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
        data-title="{{ trans('icore::profile.password_confirmation') }}">
            {{ trans('icore::profile.password_button') }}
        </a>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-3 col-form-label text-lg-left">{{ trans('icore::profile.change_email') }}</label>
    <div class="col-lg-6">
        <button type="button" data-toggle="modal" data-target="#editProfileEmailModal"
        class="btn btn-outline-primary">
            {{ trans('icore::profile.email_button') }}
        </button>
    </div>
</div>

@include('icore::web.profile.edit_email')

@endsection

@push('script')
@component('icore::web.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Web\Profile\UpdateRequest', '#updateProfile'); !!}
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Web\Profile\UpdateEmailRequest', '#updateEmailProfile'); !!}
@endcomponent
@endpush
