@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::posts.page.search', ['search' => $search]), trans('icore::pagination.page', ['num' => $posts->currentPage()])],
    'desc' => [trans('icore::posts.page.search', ['search' => $search])],
    'keys' => [trans('icore::posts.page.search', ['search' => $search])]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('web.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('web.post.index') }}">{{ trans('icore::posts.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::posts.page.search', ['search' => $search]) }}</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2 mb-4">
                {{ trans('icore::posts.page.search', ['search' => $search]) }}
            </h1>
            @if ($posts->isNotEmpty())
            <div id="infinite-scroll">
                @foreach ($posts as $item)
                    @include('icore::web.post.post', ['post' => $item])
                @endforeach
                @include('icore::admin.partials.pagination', ['items' => $posts])
            </div>
            @else
            <p>{{ trans('icore::default.empty') }}</p>
            @endif
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @include('icore::web.partials.sidebar')
        </div>
    </div>
</div>
@endsection
