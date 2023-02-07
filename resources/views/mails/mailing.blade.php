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

{!! $content !!}

{{-- Subcopy --}}
@isset($subcopy)
@component('mail::subcopy')
{!! $subcopy !!}
@endcomponent
@endisset

@endcomponent
