@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::contact.route.show')],
    'desc' => [trans('icore::contact.route.show')],
    'keys' => [trans('icore::contact.route.show')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::contact.route.show') }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('icore::web.partials.alerts')
            <form method="post" action="{{ url()->current() }}" id="contact">
                @csrf
                <div class="form-group">
                    <label for="email">{{ trans('icore::contact.address.label') }}</label>
                    <input type="email" name="email" id="email" 
                    value="{{ old('email', auth()->user()->email ?? null) }}"
                    class="form-control @isValid('email')"
                    placeholder="{{ trans('icore::contact.address.placeholder') }}">
                    @includeWhen($errors->has('email'), 'icore::web.partials.errors', ['name' => 'email'])
                </div>
                <div class="form-group">
                    <label for="title">{{ trans('icore::contact.title.label') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                    class="form-control @isValid('title')"
                    placeholder="{{ trans('icore::contact.title.placeholder') }}">
                    @includeWhen($errors->has('title'), 'icore::web.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label for="content">{{ trans('icore::contact.content') }}</label>
                    <textarea name="content" id="content"
                    class="form-control @isValid('content')"
                    rows="3">{{ old('content') }}</textarea>
                    @includeWhen($errors->has('content'), 'icore::web.partials.errors', ['name' => 'content'])
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input @isValid('contact_agreement')" 
                        id="contact_agreement" name="contact_agreement" value="1"
                        {{ old('contact_agreement') == true ? 'checked' : null }}>
                        <label class="custom-control-label text-left" for="contact_agreement">
                            <small>{{ trans('icore::policy.agreement.contact') }}</small>
                        </label>
                    </div>
                    @includeWhen($errors->has('contact_agreement'), 'icore::web.partials.errors', ['name' => 'contact_agreement'])
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
            <div>
                @render('icore::map.mapComponent', [
                    'address_marker' => ['Chmielna 27/33, 80-748 Gdańsk']
                ])
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@component('icore::web.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Web\Contact\SendRequest', '#contact'); !!}
@endcomponent
@endpush
