<div class="mb-5" id="row{{ $post->id }}">
    <h2 class="h5 border-bottom pb-2">
        <a 
            href="{{ route('web.post.show', [$post->slug]) }}" 
            title="{{ $post->title }}"
        >
            {{ $post->title }}
        </a>
    </h2>
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
