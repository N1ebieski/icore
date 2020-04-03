@guest
@if (!request()->cookie('policyAgree'))
<div id="policy">
    <div class="policy-height"></div>
    <nav class="navbar policy fixed-bottom navbar-light bg-light border-top">
        <div class="navbar-text py-0">
            <small>
                {!! trans('icore::policy.info', [
                    'privacy' => route('web.page.show', [Str::slug(trans('icore::policy.privacy'))])
                ]) !!}
            </small>
        </div>
    </nav>
</div>
@endif
@endguest