<div 
    class="{{ ($comment->real_depth < 5 && $comment->real_depth > 0) ? 'depth-1' : '' }}"
    id="row{{ $comment->id }}" 
    data-id="{{ $comment->id }}"
>
    <div id="comment{{ $comment->id }}" class="transition my-3">
        <div>
            <div class="d-flex mb-2">
                <small class="mr-auto">
                    {{ trans('icore::comments.created_at_diff') }}: {{ $comment->created_at_diff }}
                </small>
                @if ($comment->user)
                <small class="ml-auto text-right">
                    {{ trans('icore::comments.author') }}: {{ $comment->user->name }}
                </small>
                @endif
            </div>
            <div>
                @if ($comment->censored == true)
                <em>{{ trans('icore::comments.censored') }}</em>
                @else
                {!! $comment->content_as_html !!}
                @endif
            </div>
            <div class="d-flex my-2">
                <small class="mr-auto">
                    <span 
                        class="rating
                        {{ (auth()->user() && $comment->ratings->contains('user_id', auth()->user()->id)) ? 'font-weight-bold' : '' }}"
                    >
                        {{ $comment->ratings->sum('rating') }}
                    </span>
                    &nbsp;|&nbsp;
                    @auth
                    <a 
                        class="rateComment" 
                        href="#" 
                        title="{{ trans('icore::ratings.plus') }}"
                        data-route="{{ route('web.rating.comment.rate', [$comment->id, 'rating' => 1]) }}"
                    >
                        <i class="fas fa-angle-up"></i>
                    </a>
                    &nbsp;
                    <a 
                        class="rateComment" 
                        href="#" 
                        title="{{ trans('icore::ratings.minus') }}"
                        data-route="{{ route('web.rating.comment.rate', [$comment->id, 'rating' => -1]) }}"
                    >
                        <i class="fas fa-angle-down"></i>
                    </a>
                    @canany(['web.comments.create', 'web.comments.suggest'])
                    &nbsp;|&nbsp;
                    <a 
                        class="createComment" 
                        href="#" 
                        title="{{ trans('icore::comments.answer') }}"
                        data-route="{{ route("web.comment.{$comment->poli}.create", [$comment->model_id, 'parent_id' => $comment->id]) }}"
                    >
                        {{ trans('icore::comments.answer') }}
                    </a>
                    @endcanany
                    @can('web.comments.edit')
                    @can('update', $comment)
                    &nbsp;|&nbsp;
                    <a 
                        class="editComment" 
                        href="#" 
                        title="{{ trans('icore::comments.edit') }}"
                        data-route="{{ route('web.comment.edit', [$comment->id]) }}"
                    >
                        {{ trans('icore::comments.edit') }}
                    </a>
                    @endcan
                    @endcan
                    @else
                    <a 
                        href="{{ route('login') }}" 
                        title="{{ trans('icore::comments.log_to_answer') }}"
                    >
                        {{ trans('icore::comments.log_to_answer') }}
                    </a>
                    @endauth
                </small>
                @auth
                <small class="ml-auto">
                    <a 
                        class="createReport" 
                        href="#" 
                        title="{{ trans('icore::comments.report') }}"
                        data-route="{{ route('web.report.comment.create', [$comment->id]) }}"
                        data-toggle="modal" 
                        data-target="#createReportModal"
                    >
                        {{ trans('icore::comments.report') }}
                    </a>
                </small>
                @endauth
            </div>
        </div>
    </div>
    @if ($comment->childrens_count > 0)
    <div>
        <a 
            href="#" 
            title="{{ trans('icore::comments.next_answers') }}"
            data-route="{{ route('web.comment.take', [$comment->id]) }}" 
            role="button"
            class="btn btn-outline-secondary text-nowrap depth-1 takeComment"
        >
            <span>{{ trans('icore::comments.next_answers') }} </span>
            <i class="fas fa-angle-down"></i>
        </a>
    </div>
    @endif
    @if ($comment->relationLoaded('childrens'))
        @foreach ($comment->childrens as $children)
            @include('icore::web.comment.partials.comment', ['comment' => $children])
        @endforeach
        @if ($comment->childrens->currentPage() < $comment->childrens->lastPage())
        <div class="mt-3">
            <a 
                href="#" 
                title="{{ trans('icore::comments.next_comments') }}"
                data-route="{{ route('web.comment.take', [$comment->id]) }}" 
                role="button"
                class="btn btn-outline-secondary text-nowrap depth-1 takeComment"
            >
                <span>{{ trans('icore::comments.next_comments') }} </span>
                <i class="fas fa-angle-down"></i>
            </a>
        </div>
        @endif
    @endif
</div>
