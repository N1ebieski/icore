@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::posts.route.index'), trans('icore::pagination.page', ['num' => $posts->currentPage()])],
    'desc' => [trans('icore::posts.route.index')],
    'keys' => [trans('icore::posts.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::posts.route.index') }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            @if ($posts->isNotEmpty())
            <div id="infinite-scroll">
                @foreach ($posts as $post)
                    @include('icore::web.post.partials.post', [$post])
                @endforeach
                @include('icore::web.partials.pagination', ['items' => $posts, 'next' => true])
            </div>
            @else
            <p>{{ trans('icore::default.empty') }}</p>
            @endif
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @include('icore::web.post.partials.sidebar')
        </div>
    </div>
</div>
@endsection
