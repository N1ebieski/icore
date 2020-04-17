<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>

    <title>{{ app('icore.helpers.view')->makeMeta(array_merge($title, [trans('icore::admin.route.index'), config('app.name')]), ' - ') }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ app('icore.helpers.view')->makeMeta(array_merge($desc, [trans('icore::admin.route.index'), config('app.desc')]), '. ') }}">
    <meta name="keywords" content="{{ mb_strtolower(app('icore.helpers.view')->makeMeta(array_merge($keys, [trans('icore::admin.route.index'), config('app.keys')]), ', ')) }}">
    <meta name="robots" content="noindex, nofollow">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('svg/vendor/icore/logo.svg') }}" type="image/svg+xml">
    <link href="{{ mix('css/vendor/icore/vendor/vendor.css') }}" rel="stylesheet">
    <link href="{{ mix(app('icore.helpers.view')->getStylesheet()) }}" rel="stylesheet">

    <script src="{{ mix('js/vendor/icore/vendor/vendor.js') }}" defer></script>
    <script src="{{ mix('js/vendor/icore/admin/admin.js') }}" defer></script>

</head>
<body>

    @include('icore::admin.partials.nav')

    <div class="wrapper">

        @include('icore::admin.partials.sidebar')

        <div class="content-wrapper">

            <div class="menu-height"></div>

            <div class="container-fluid">
                @include('icore::admin.partials.breadcrumb')
                @include('icore::admin.partials.alerts')
                @yield('content')
            </div>

            @include('icore::admin.partials.footer')

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="{{ mix('js/vendor/icore/admin/scripts.js') }}" defer></script>
    @stack('script')

</body>
</html>
