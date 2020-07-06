@hasSection('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white px-0">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.home.index') }}" title="Dashboard">
                Dashboard
            </a>
        </li>
        @yield('breadcrumb')
    </ol>
</nav>
@endif
