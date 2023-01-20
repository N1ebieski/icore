<nav class="navbar navbar-expand navbar-light bg-light fixed-top border-bottom">
    <a href="#" class="navbar-toggler" role="button" id="sidebar-toggle">
        <span class="navbar-toggler-icon"></span>
    </a>
    <a 
        href="{{ route('web.home.index') }}"
        class="navbar-brand"
        title="{{ config('app.name') }}"
    >
        <img 
            src="{{ asset('images/vendor/icore/logo.svg') }}" 
            class="pb-1 pr-1 logo" 
            alt="{{ config('app.name_short') }}" 
            title="{{ config('app.name') }}"
        >
        <span class="d-none d-md-inline">{{ config('app.name_short') }}</span>
    </a>
    <ul class="navbar-nav ml-auto">
        @if (count(config('icore.multi_langs')) > 1)
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="dropdown-multi-lang"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <span class="fi fil-{{ $currentLang }} pb-1"></span>
                <span class="d-sm-inline d-none">{{ mb_strtoupper($currentLang) }}</span>
                <span class="d-inline">
                    @if ($count = $inactiveCount->sum('count'))
                    <span class="badge badge-warning">{{ $count }}</span>
                    @endif
                    @if ($count = $reportedCount->sum('count'))
                    <span class="badge badge-danger">{{ $count }}</span>
                    @endif
                </span>             
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right" 
                id="dropdown-multi-lang-toggle"
                aria-labelledby="dropdown-multi-lang"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::default.lang_toggle') }}:
                </h6>            
                @foreach ($langs as $lang)
                <a 
                    class="dropdown-item {{ $isCurrentLang($lang) }}" 
                    data-lang="{{ $lang }}"
                    href="{{ $getCurrentUrlWithLang($lang) }}" 
                    title="{{ $lang }}"
                >
                    <span class="fi fil-{{ $lang }}"></span>
                    <span>{{ mb_strtoupper($lang) }}</span>
                    <span class="d-inline">
                        @if ($count = $inactiveCount->firstWhere('lang', $lang))
                        <span class="badge badge-warning">{{ $count->count }}</span>
                        @endif
                        @if ($count = $reportedCount->firstWhere('lang', $lang))
                        <span class="badge badge-danger">{{ $count->count }}</span>
                        @endif
                    </span>
                </a>
                @endforeach                       
            </div>
        </li>
        @endif
        @if (count(config('icore.multi_themes')) > 1)
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="dropdown-multi-theme"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <span class="fas fa-lg fa-icore-{{ $currentTheme }}"></span>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right"
                id="dropdown-multi-theme-toggle"
                aria-labelledby="dropdown-multi-theme"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::default.theme_toggle') }}:
                </h6>
                @foreach ($themes as $theme)
                <a 
                    class="dropdown-item {{ $isCurrentTheme($theme) }}"
                    data-theme="{{ $theme }}"
                    href="#{{ $theme }}" 
                    title="{{ trans('icore::default.' . $theme) }}"
                >
                    <span class="fas fa-icore-{{ $theme }}"></span>
                    <span>{{ trans('icore::default.' . $theme) }}</span>
                </a>
                @endforeach                       
            </div>
        </li>
        @endif
        <li class="nav-item dropdown">
            <a 
                class="nav-link text-nowrap" 
                href="#" 
                role="button" 
                id="navbar-dropdown-menu-profile"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-lg fa-users-cog"></i>
            </a>
            <div 
                class="dropdown-menu dropdown-menu-right" 
                aria-labelledby="navbar-dropdown-menu-profile"
            >
                <h6 class="dropdown-header">
                    {{ trans('icore::auth.hello')}}, {{ auth()->user()->name }}!
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
