@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::posts.route.search', ['search' => $search]), trans('icore::pagination.page', ['num' => $posts->currentPage()])],
    'desc' => [trans('icore::posts.route.search', ['search' => $search])],
    'keys' => [trans('icore::posts.route.search', ['search' => $search])]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('web.post.index') }}" title="{{ trans('icore::posts.route.index') }}">
        {{ trans('icore::posts.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::posts.route.search', ['search' => $search]) }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2 mb-4">
                {{ trans('icore::posts.route.search', ['search' => $search]) }}
            </h1>
            @if ($posts->isNotEmpty())
            <div id="infinite-scroll">
                @foreach ($posts as $item)
                    @include('icore::web.post.partials.post', ['post' => $item])
                @endforeach
                @include('icore::web.partials.pagination', ['items' => $posts])
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
