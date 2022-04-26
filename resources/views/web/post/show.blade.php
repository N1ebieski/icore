@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [
        $post->meta_title,
        $post->comment->isActive() ? 
            (
                $comments->currentPage() > 1 ?
                    trans('icore::pagination.page', ['num' => $comments->currentPage()])
                    : null
            )
            : null
    ],
    'desc' => [$post->meta_desc],
    'keys' => [$post->tag_list],
    'index' => $post->seo_noindex->isActive() ? 'noindex' : 'index',
    'follow' => $post->seo_nofollow->isActive() ? 'nofollow' : 'follow',
    'og' => [
        'title' => $post->meta_title,
        'desc' => $post->meta_desc,
        'image' => $post->first_image
    ]
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
<li class="breadcrumb-item active" aria-current="page">
    {{ $post->title }}
</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <div class="mb-5">
                <h1 class="h4 border-bottom pb-2">
                    {{ $post->title }}
                </h1>
                <div class="d-flex mb-2">
                    <small class="mr-auto">
                        {{ trans('icore::posts.published_at_diff') }}: {{ $post->published_at_diff }}
                    </small>
                    <small class="ml-auto text-right">
                        {{ trans('icore::posts.author') }}: {{ $post->user->name ?? '' }}
                    </small>
                </div>
                <div class="post">
                    {!! $post->no_more_content_html !!}
                </div>
                <div class="d-flex mb-2">
                    @if ($post->categories->isNotEmpty())
                    <small class="mr-auto">
                        {{ trans('icore::categories.categories.label') }}:
                        @foreach ($post->categories as $category)
                        <a 
                            href="{{ route('web.category.post.show', [$category->slug]) }}"
                            title="{{ $category->name }}"
                        >
                            {{ $category->name }}
                        </a>
                        <span>{{ (!$loop->last) ? ', ' : '' }}</span>
                        @endforeach
                    </small>
                    @endif
                    @if ($post->tags->isNotEmpty())
                    <small class="ml-auto text-right">
                        {{ trans('icore::posts.tags.label') }}:
                        @foreach ($post->tags as $tag)
                        <a 
                            href="{{ route('web.tag.post.show', [$tag->normalized]) }}"
                            title="{{ $tag->name }}"
                        >
                            {{ $tag->name }}
                        </a>
                        <span>{{ (!$loop->last) ? ', ' : '' }}</span>
                        @endforeach
                    </small>
                    @endif
                </div>
                <div class="d-flex my-3">
                    @if (isset($previous))
                    <a 
                        class="mr-auto" 
                        href="{{ route('web.post.show', [$previous->slug]) }}"
                        title="&laquo; {{ $previous->title }}"
                    >
                        &laquo; {{ $previous->title }}
                    </a>
                    @endif
                    @if (isset($next))
                    <a 
                        class="ml-auto text-right" 
                        href="{{ route('web.post.show', [$next->slug]) }}"
                        title="{{ $next->title }} &raquo;"
                    >
                        {{ $next->title }} &raquo;
                    </a>
                    @endif
                </div>
                @if ($related->isNotEmpty())
                <h3 class="h5">
                    {{ trans('icore::posts.related') }}
                </h3>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($related as $rel)
                    <li class="list-group-item">
                        <a 
                            href="{{ route('web.post.show', [$rel->slug]) }}"
                            title="{{ $rel->title }}"
                        >
                            {{ $rel->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
                @if ($post->comment->isActive())
                <h3 class="h5 border-bottom pb-2" id="comments">
                    {{ trans('icore::comments.comments') }}
                </h3>
                <div id="filter-content">
                    @if ($comments->isNotEmpty())
                        @include('icore::web.comment.partials.filter')
                    @endif
                    <div id="comment">
                        @auth
                        @canany(['web.comments.create', 'web.comments.suggest'])
                        @include('icore::web.comment.create', [
                            'model' => $post, 
                            'parent_id' => 0
                        ])
                        @endcanany
                        @else
                        <a 
                            href="{{ route('login') }}" 
                            title="{{ trans('icore::comments.log_to_comment') }}"
                        >
                            {{ trans('icore::comments.log_to_comment') }}
                        </a>
                        @endauth
                    </div>
                    @if ($comments->isNotEmpty())
                    <div id="infinite-scroll">
                        @foreach ($comments as $comment)
                            @include('icore::web.comment.partials.comment', [
                                'comment' => $comment
                            ])
                        @endforeach
                        @include('icore::web.partials.pagination', [
                            'items' => $comments,
                            'fragment' => 'comments'
                        ])
                    </div>
                    @endif
                </div>
                @component('icore::web.partials.modal')
                @slot('modal_id', 'create-report-modal')
                @slot('modal_title')
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ trans('icore::reports.route.create') }}</span>
                @endslot
                @endcomponent
                @endif
            </div>
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @if ($post->relationLoaded('stats'))
            <div class="list-group list-group-flush mb-3">
                @foreach ($post->stats as $stat)
                <div class="list-group-item">
                    <div class="float-left mr-2">
                        {{ trans("icore::stats.{$stat->slug}") }}:
                    </div>
                    <div class="float-right">
                        {{ $stat->pivot->value }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif        
            @include('icore::web.post.partials.sidebar')
        </div>
    </div>
</div>
@endsection
