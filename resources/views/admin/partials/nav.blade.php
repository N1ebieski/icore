<nav class="navbar navbar-expand navbar-light bg-light fixed-top border-bottom">
    <a href="#" class="navbar-toggler" role="button" id="sidebar-toggle">
        <span class="navbar-toggler-icon"></span>
    </a>
    <a href="/" class="navbar-brand" title="{{ config('app.name') }}">
        <img 
            src="{{ asset('images/vendor/icore/logo.svg') }}" 
            class="pb-1 pr-1 logo" 
            alt="{{ config('app.name_short') }}" 
            title="{{ config('app.name') }}"
        >
        <span>{{ config('app.name_short') }}</span>
    </a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item mx-1 dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="dropdown-theme"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <span class="fas fa-lg fa-{{ $themes[$currentTheme] }}"></span>
                <span class="d-inline d-md-none">{{ trans('icore::default.' . $currentTheme) }}</span>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right"
                id="theme-dropdown-toggle"
                aria-labelledby="dropdown-theme"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::default.theme_toggle') }}:
                </h6>
                @foreach ($themes as $theme => $icon)
                <a 
                    class="dropdown-item {{ $isCurrentTheme($theme) }}"
                    data-theme="{{ $theme }}"
                    href="#{{ $theme }}" 
                    title="{{ trans('icore::default.' . $theme) }}"
                >
                    <span class="fas fa-{{ $icon }}"></span>
                    <span>{{ trans('icore::default.' . $theme) }}</span>
                </a>
                @endforeach                       
            </div>
        </li>
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="navbar-dropdown-menu-link"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-lg fa-users-cog"></i>
                <span class="d-none d-sm-inline">{{ auth()->user()->short_name }}</span>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right" 
                aria-labelledby="navbar-dropdown-menu-link"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::auth.hello')}}, {{ auth()->user()->name }}
                </h6>
                <a 
                    class="dropdown-item {{ $isUrl(route('web.profile.edit')) }}" 
                    href="{{ route('web.profile.edit') }}" 
                    title="{{ trans('icore::profile.route.edit') }}"
                >
                    {{ trans('icore::profile.route.index') }}
                </a>
                @can('admin.home.view')
                <a 
                    class="dropdown-item" 
                    href="{{ route('admin.home.index') }}"
                    title="{{ trans('icore::admin.route.index') }}"
                >
                    {{ trans('icore::admin.route.index') }}
                </a>
                @endcan
                <div class="dropdown-divider"></div>
                <form 
                    class="d-inline" 
                    method="POST" 
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button type="submit" class="btn btn-link dropdown-item">
                        {{ trans('icore::auth.route.logout') }}
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
