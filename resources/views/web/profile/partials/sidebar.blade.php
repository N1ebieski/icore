<ul class="sidebar navbar-nav border-right">
    <li class="nav-item @isUrl(route('web.profile.edit'))">
        <a class="nav-link" href="{{ route('web.profile.edit') }}">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>{{ trans('icore::profile.page.edit') }}</span>
        </a>
    </li>
    <li class="nav-item @isUrl(route('web.profile.edit_socialite'))">
        <a class="nav-link" href="{{ route('web.profile.edit_socialite') }}">
            <i class="fab fa-fw fa-facebook-square"></i>
            <span>{{ trans('icore::profile.page.edit_socialite') }}</span>
        </a>
    </li>
</ul>
