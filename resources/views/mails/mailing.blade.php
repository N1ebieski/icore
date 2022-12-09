@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if (isset($level) && $level === 'error')
# @lang('Whoops!')
@else
# @lang(trans('icore::auth.hello').'!')
@endif
@endif

{!! $mailingEmail->mailing->replacement_content_html !!}

{{-- Subcopy --}}
@isset($subcopy)
@component('mail::subcopy')
{!! $subcopy !!}
@endcomponent
@endisset

@endcomponent
