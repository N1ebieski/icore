@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [
        trans('icore::tags.route.show', ['tag' => $tag->name]),
        $posts->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $posts->currentPage()])
            : null
    ],
    'desc' => [trans('icore::tags.route.show', ['tag' => $tag->name])],
    'keys' => [trans('icore::tags.route.show', ['tag' => $tag->name])],
    'index' => 'noindex'
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('web.post.index') }}" 
        title="{{ trans('icore::posts.route.index') }}"
    >
        {{ trans('icore::posts.route.index') }}
    </a>
</li>
<li class="breadcrumb-item">
    {{ trans('icore::tags.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ $tag->name }}
</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2 mb-4">
                {{ trans('icore::tags.route.show', ['tag' => $tag->name]) }}
            </h1>
            @if ($posts->isNotEmpty())
            <div id="infinite-scroll">
                @foreach ($posts as $post)
                    @include('icore::web.post.partials.post', [$post])
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
