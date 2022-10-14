@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [
        $page->meta_title,
        $page->comment->isActive() ?
            (
                $comments->currentPage() > 1 ?
                    trans('icore::pagination.page', ['num' => $comments->currentPage()])
                    : null
            )
            : null
    ],
    'desc' => [$page->meta_desc],
    'keys' => [$page->tag_list],
    'index' => $page->seo_noindex->isActive() ? 'noindex' : 'index',
    'follow' => $page->seo_nofollow->isActive() ? 'nofollow' : 'follow',
    'og' => [
        'title' => $page->meta_title,
        'desc' => $page->meta_desc,
        'image' => $page->first_image
    ]
])

@section('breadcrumb')
@if ($page->ancestors->isNotEmpty())
    @foreach ($page->ancestors as $ancestor)
        <li class="breadcrumb-item">
            @if (!empty($ancestor->content))
            <a 
                href="{{ route('web.page.show', [$ancestor->slug]) }}"
                title="{{ $ancestor->title }}"
            >
                {{ $ancestor->title }}
            </a>
            @else
                {{ $ancestor->title }}
            @endif
        </li>
    @endforeach
@endif
<li class="breadcrumb-item active" aria-current="page">
    {{ $page->title }}
</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 order-sm-1 order-md-2">
            <div class="mb-5">
                <div class="d-flex justify-content-between">
                    <h1 class="h4 border-bottom pb-2">
                        @if (!empty($page->icon))
                        <i class="{{ $page->icon }}"></i>
                        @endif
                        <span>{{ $page->title }}</span>
                    </h1>
                    @can ('admin.pages.view')
                    <div>
                        <a
                            href="{{ route('admin.page.index', ['filter[search]' => 'id:"' . $page->id . '"']) }}"
                            target="_blank"
                            rel="noopener"
                            title="{{ trans('icore::pages.route.index') }}"
                            class="badge badge-primary"
                        >
                            {{ trans('icore::default.admin') }}
                        </a>
                    </div>
                    @endcan
                </div>
                <div>
                    {!! $page->no_more_content_html !!}
                </div>
                @if ($page->comment->isActive())
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
                            'model' => $page,
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
                    @component('icore::web.partials.modal')
                    @slot('modal_id', 'create-report-modal')
                    @slot('modal_title')
                    <span>{{ trans('icore::reports.route.create') }}</span>
                    @endslot
                    @endcomponent
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-4 order-sm-2 order-md-1">
            @if ($page->relationLoaded('stats'))
            <div class="list-group list-group-flush mb-3">
                @foreach ($page->stats->whereNotIn('slug', [Stat\Slug::VISIT]) as $stat)
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
            @include('icore::web.page.partials.sidebar', ['page' => $page])
        </div>
    </div>
</div>
@endsection

@if (!empty(config('icore.captcha.driver')))
@php
app(\N1ebieski\ICore\View\Components\CaptchaComponent::class)->render()->render();
@endphp
@endif
