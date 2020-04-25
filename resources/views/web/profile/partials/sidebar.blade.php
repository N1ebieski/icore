<h3 class="h5 d-md-none">{{ trans('icore::profile.pages') }}</h3>
<ul class="sidebar navbar-nav h-100">
    <li class="nav-item @isUrl(route('web.profile.edit'))">
        <a class="nav-link @isUrl(route('web.profile.edit'))"
        href="{{ route('web.profile.edit') }}">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>{{ trans('icore::profile.route.edit') }}</span>
        </a>
    </li>
    @if (app('router')->has('web.profile.edit_socialite'))
    <li class="nav-item @isUrl(route('web.profile.edit_socialite'))">
        <a class="nav-link @isUrl(route('web.profile.edit_socialite'))"
        href="{{ route('web.profile.edit_socialite') }}">
            <i class="fab fa-fw fa-facebook-square"></i>
            <span>{{ trans('icore::profile.route.edit_socialite') }}</span>
        </a>
    </li>
    @endif
</ul>
