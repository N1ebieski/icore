@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::filemanager.route.index')],
    'desc' => [trans('icore::filemanager.route.index')],
    'keys' => [trans('icore::filemanager.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::filemanager.route.index') }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="far fa-fw fa-image"></i>
        <span>{{ trans('icore::filemanager.route.index') }}</span>
    </div>
</h1>
<div style="height: 600px;">
    <div id="fm"></div>
</div>

@push('style')
<link 
    rel="stylesheet" 
    href="{{ asset('css/vendor/file-manager/file-manager.css') }}"
>
@endpush

@push('script')
<script 
    type="text/javascript"
    src="{{ asset('js/vendor/file-manager/file-manager.js') }}"
    defer
></script>
@endpush

@endsection
