@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::contact.page.index')],
    'desc' => [trans('icore::contact.page.index')],
    'keys' => [trans('icore::contact.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::contact.page.index') }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('icore::web.partials.alerts')
            <form method="post" action="{{ route('web.contact.index') }}" id="contact">
                @csrf
                <div class="form-group">
                    <label for="email">{{ trans('icore::contact.address') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="form-control @isValid('email')"
                    placeholder="{{ trans('icore::contact.enter_address') }}">
                    @includeWhen($errors->has('email'), 'icore::web.partials.errors', ['name' => 'email'])
                </div>
                <div class="form-group">
                    <label for="title">{{ trans('icore::contact.title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                    class="form-control @isValid('title')"
                    placeholder="{{ trans('icore::contact.enter_title') }}">
                    @includeWhen($errors->has('title'), 'icore::web.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label for="content">{{ trans('icore::contact.content') }}</label>
                    <textarea name="content" id="content"
                    class="form-control @isValid('content')"
                    rows="3">{{ old('content') }}</textarea>
                    @includeWhen($errors->has('content'), 'icore::web.partials.errors', ['name' => 'content'])
                </div>
                @render('icore::captchaComponent')
                <button type="submit" class="btn btn-primary btn-send">{{ trans('icore::default.submit') }}</button>
            </form>
        </div>
        <hr class="clearfix w-100 d-md-none">
        <div class="col-md-4">
            <h3 class="h5">{{ trans('icore::contact.details') }}:</h3>
            <p>
                XXXXX XXXXXXXXXXX<br>
                ul. XXXXXXXXXXX XX/YY<br>
                XXXXX XX-XXX<br>
                tel. XXX-XXX-XX-XX<br>
                e-mail: xxxxxxx@xxxxxxxxx.pl<br>
            </p>
        </div>
    </div>
</div>
@endsection

@push('script')
@component('icore::web.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Web\Contact\SendRequest', '#contact'); !!}
@endcomponent
@endpush
