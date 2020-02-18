@component('mail::message')

{{ strip_tags($content) }}

@component('mail::subcopy')
{{ $subcopy }}
@endcomponent

@endcomponent
