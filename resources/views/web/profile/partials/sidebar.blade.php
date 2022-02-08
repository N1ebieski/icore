<h3 class="h5 d-sm-none">
    {{ trans('icore::profile.pages') }}
</h3>
<ul class="sidebar navbar-nav h-100">
    <li class="nav-item {{ $isUrl(route('web.profile.edit')) }}">
        <a 
            class="nav-link {{ $isUrl(route('web.profile.edit')) }}"
            title="{{ trans('icore::profile.route.edit') }}"
            href="{{ route('web.profile.edit') }}"
        >
            <i class="fas fa-fw fa-user-edit"></i>
            <span>{{ trans('icore::profile.route.edit') }}</span>
        </a>
    </li>
    @if (app('router')->has('web.profile.socialites'))
    <li class="nav-item {{ $isUrl(route('web.profile.socialites')) }}">
        <a 
            class="nav-link {{ $isUrl(route('web.profile.socialites')) }}"
            title="{{ trans('icore::profile.route.edit_socialite') }}"
            href="{{ route('web.profile.socialites') }}"
        >
            <i class="fab fa-fw fa-facebook-square"></i>
            <span>{{ trans('icore::profile.route.edit_socialite') }}</span>
        </a>
    </li>
    @endif
    @canany(['web.tokens.edit', 'web.tokens.delete'])
    @can('api.access')
    <li class="nav-item {{ $isUrl(route('web.profile.tokens')) }}">
        <a 
            class="nav-link {{ $isUrl(route('web.profile.tokens')) }}"
            title="{{ trans('icore::profile.route.tokens') }}"
            href="{{ route('web.profile.tokens') }}"
        >
            <i class="fas fa-fw fa-user-lock"></i>
            <span>{{ trans('icore::profile.route.tokens') }}</span>
        </a>
    </li>
    @endcan
    @endcan
</ul>
