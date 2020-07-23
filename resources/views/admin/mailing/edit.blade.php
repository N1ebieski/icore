@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::mailings.route.edit')],
    'desc' => [trans('icore::mailings.route.edit')],
    'keys' => [trans('icore::mailings.route.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.mailing.index') }}" title="{{ trans('icore::mailings.route.index') }}">
        {{ trans('icore::mailings.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::mailings.route.edit') }}</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="fas fa-edit"></i>&nbsp;{{ trans('icore::mailings.route.edit') }}:
    </h1>
    <form class="mb-3" method="post" action="{{ route('admin.mailing.update', [$mailing->id]) }}" id="editMailing">
        @csrf
        @method('put')
        <div class="row">
            <div class="col-lg-9 form-group">
                <div class="form-group">
                    <label for="title">{{ trans('icore::mailings.title') }}</label>
                    <input type="text" value="{{ old('title', $mailing->title) }}" name="title" id="title" class="form-control {{ $isValid('title') }}">
                    @includeWhen($errors->has('title'), 'icore::admin.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label class="d-flex justify-content-between" for="content_html_trumbowyg">
                        <div>{{ trans('icore::mailings.content') }}:</div>
                        @include('icore::admin.partials.counter', [
                            'string' => old('content_html', $mailing->content_html),
                            'name' => 'content_html'
                        ])
                    </label>
                    <div class="{{ $isTheme('dark', 'trumbowyg-dark') }}">
                        <textarea name="content_html" id="content_html_trumbowyg" class="form-control {{ $isValid('content_html') }}"
                        rows="10" id="content_html" data-lang="{{ config('app.locale') }}">{{ old('content_html', $mailing->content_html) }}</textarea>
                    </div>
                    @includeWhen($errors->has('content_html'), 'icore::admin.partials.errors', ['name' => 'content_html'])
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="status">{{ trans('icore::filter.status.label') }}</label>
                    <select class="custom-select" id="status" name="status"
                    data-toggle="collapse" aria-expanded="false" aria-controls="collapseActivationAt">
                        <option value="{{ $mailing::ACTIVE }}" {{ (old('status', $mailing->status) == $mailing::ACTIVE) ? 'selected' : '' }}>
                            {{ trans('icore::filter.active') }}
                        </option>
                        <option value="{{ $mailing::INACTIVE }}" {{ (old('status', $mailing->status) == $mailing::INACTIVE) ? 'selected' : '' }}>
                            {{ trans('icore::filter.inactive') }}
                        </option>
                        <option value="{{ $mailing::SCHEDULED }}" {{ (old('status', $mailing->status) == $mailing::SCHEDULED) ? 'selected' : '' }}>
                            {{ trans('icore::filter.scheduled') }}
                        </option>
                    </select>
                </div>
                <div class="form-group collapse {{ (old('status', $mailing->status) == 2) ? 'show' : '' }}"
                id="collapseActivationAt">
                    <label for="activation_at">
                        {{ trans('icore::mailings.activation_at.label') }} <i data-toggle="tooltip" data-placement="top"
                        title="{{ trans('icore::mailings.activation_at.tooltip') }}" class="far fa-question-circle"></i>
                    </label>
                    <div id="activation_at">
                        <div class="form-group">
                            <input type="text" data-value="{{ Carbon\Carbon::parse(old('date_activation_at', $mailing->activation_at))->format('Y/m/d') }}"
                            value="" name="date_activation_at" id="date_activation_at" class="form-control datepicker"
                            data-lang="{{ config('app.locale') }}">
                            @includeWhen($errors->has('date_activation_at'), 'icore::admin.partials.errors', ['name' => 'date_activation_at'])
                        </div>
                        <div class="form-group">
                            <input type="text" data-value="{{ Carbon\Carbon::parse(old('time_activation_at', $mailing->activation_at))->format('H:i') }}"
                            value="" name="time_activation_at" id="time_activation_at" class="form-control timepicker"
                            data-lang="{{ config('app.locale') }}">
                            @includeWhen($errors->has('time_activation_at'), 'icore::admin.partials.errors', ['name' => 'time_activation_at'])
                        </div>
                    </div>
                </div>
                @if ($mailing->emails->count() === 0)
                <div class="mb-3">{{ trans('icore::mailings.recipients') }}:</div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="users"
                        {{ (old('users') == true) ? 'checked' : '' }} name="users" value="true">
                        <label class="custom-control-label" for="users">{{ trans('icore::mailings.users') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="newsletters"
                        {{ (old('newsletters') == true) ? 'checked' : '' }} name="newsletters" value="true">
                        <label class="custom-control-label" for="newsletters">{{ trans('icore::mailings.subscribers') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="emails"
                        name="emails" value="true" {{ (old('emails') == true) ? 'checked' : '' }}
                        data-toggle="collapse" data-target="#collapseEmails_Json" aria-expanded="false" aria-controls="collapseEmails_Json">
                        <label class="custom-control-label" for="emails">{{ trans('icore::mailings.custom') }}</label>
                    </div>
                </div>
                <div class="form-group collapse {{ (old('emails') == true) ? 'show' : '' }}"
                id="collapseEmails_Json">
                    <div class="form-group">
                        <label for="emails_json">{{ trans('icore::mailings.emails_json') }}</label>
                        <textarea name="emails_json" class="form-control {{ $isValid('emails_json') }}"
                        rows="10" id="emails_json">{{ old('emails_json') }}</textarea>
                        @includeWhen($errors->has('emails_json'), 'icore::admin.partials.errors', ['name' => 'emails_json'])
                    </div>
                </div>
                @endif
                <hr>
                <button type="submit" class="btn btn-primary">{{ trans('icore::default.submit') }}</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Mailing\UpdateRequest', '#editMailing'); !!}
@endcomponent
@endpush
