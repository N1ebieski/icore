@component('mail::message')

{{ strip_tags($content) }}

@component('mail::subcopy')
{{ trans('icore::contact.subcopy.form', [
    'url' => route('web.contact.index')
]) }}
@endcomponent

@endcomponent
