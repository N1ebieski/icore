<div class="mb-5" id="row{{ $post->id }}">
    <div class="d-flex border-bottom mb-2 justify-content-between">
        <h2 class="h5">
            <a 
                href="{{ route('web.post.show', [$post->slug]) }}" 
                title="{{ $post->title }}"
            >
                {{ $post->title }}
            </a>
        </h2>
        @can ('admin.posts.view')
        <div>
            <a
                href="{{ route('admin.post.index', ['filter[search]' => 'id:"' . $post->id . '"']) }}"
                target="_blank"
                rel="noopener"
                title="{{ trans('icore::posts.route.index') }}"
                class="badge badge-primary"
            >
                {{ trans('icore::default.admin') }}
            </a>
        </div>
        @endcan
    </div>        
    <div class="d-flex mb-2">
        <small class="mr-auto">
            {{ trans('icore::posts.published_at_diff') }}: {{ $post->published_at_diff }}
        </small>
        <small class="ml-auto text-right">
            {{ trans('icore::posts.author') }}: {{ $post->user->name ?? '' }}
        </small>
    </div>
    <div class="post">
        {!! $post->less_content_html !!}
    </div>
</div>
