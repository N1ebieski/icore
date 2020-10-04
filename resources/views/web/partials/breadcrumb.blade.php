@hasSection('breadcrumb')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white px-0">
            <li class="breadcrumb-item">
                <a 
                    href="{{ route('web.home.index') }}" 
                    title="{{ trans('icore::home.route.index') }}"
                >
                    {{ trans('icore::home.route.index') }}
                </a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>
</div>
@endif
