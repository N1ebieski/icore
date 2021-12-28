<div class="mb-5" id="row-{{ $post->id }}">
    <h2 class="h5 border-bottom pb-2">
        <a 
            href="{{ $post->links->web }}"
            target="_blank"
            rel="noopener"
            title="{{ $post->title }}"
        >
            {{ $post->title }}
        </a>
    </h2>
    <div class="post">
        {!! $post->less_content_html !!}
    </div>
</div>