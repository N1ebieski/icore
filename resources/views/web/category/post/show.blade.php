@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [
        trans('icore::categories.route.show', ['category' => $category->name]),
        $posts->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $posts->currentPage()])
            : null
    ],
    'desc' => [trans('icore::categories.route.show', ['category' => $category->name])],
    'keys' => [trans('icore::categories.route.show', ['category' => $category->name])]
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
    {{ trans('icore::categories.route.index') }}
</li>
@if ($category->ancestors->count() > 0)
@foreach ($category->ancestors as $ancestor)
<li class="breadcrumb-item">
    @if ($ancestor->slug)
    <a 
        href="{{ route('web.category.post.show', [$ancestor->slug]) }}"
        title="{{ $ancestor->name }}"
    >
        {{ $ancestor->name }}
    </a>
    @else
        {{ trans('icore::multi_langs.no_trans') }}
    @endif
</li>
@endforeach
@endif
<li class="breadcrumb-item active" aria-current="page">
    {{ $category->name }}
</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <h1 class="h4 border-bottom pb-2 mb-4">
                @if (!empty($category->icon))
                    <i class="{{ $category->icon }}"></i>
                @endif
                <span>{{ trans('icore::categories.route.show', ['category' => $category->name]) }}</span>
            </h1>
            @if ($posts->isNotEmpty())
            <div id="infinite-scroll">
                @foreach ($posts as $post)
                    @include('icore::web.post.partials.post', [$post])
                @endforeach
                @include('icore::web.partials.pagination', [
                    'items' => $posts,
                    'next' => true
                ])
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
