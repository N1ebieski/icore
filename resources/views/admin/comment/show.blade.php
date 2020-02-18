<div>
    @if ($comment->ancestors->isNotEmpty())
        @foreach ($comment->ancestors as $ancestor)
            <div id="comment{{ $ancestor->id }}" class="transition my-3 {{ ($ancestor->real_depth < 5) ? 'depth-' . $ancestor->real_depth : 'depth-5' }}">
                <div class="d-flex mb-2">
                    <small class="mr-auto">{{ trans('icore::comments.created_at_diff') }}: {{ $ancestor->created_at_diff }}</small>
                    <small class="ml-auto">{{ trans('icore::comments.author') }}: {{ optional($ancestor->user)->name }}</small>
                </div>
                <div>
                    @if ($ancestor->censored == true)<em>@endif
                    {!! nl2br(e($ancestor->content_html)) !!}
                    @if ($ancestor->censored == true)</em>@endif
                </div>
            </div>
        @endforeach
    @endif
    <div id="comment{{ $comment->id }}" class="transition my-3 {{ ($comment->real_depth < 5) ? 'depth-' . $comment->real_depth : 'depth-5' }}">
        <div class="d-flex mb-2 font-weight-bold">
            <small class="mr-auto font-weight-bold">{{ trans('icore::comments.created_at_diff') }}: {{ $comment->created_at_diff }}</small>
            <small class="ml-auto font-weight-bold">{{ trans('icore::comments.author') }}: {{ optional($comment->user)->name }}</small>
        </div>
        <div class="font-weight-bold">
            @if ($comment->censored == true)<em>@endif
            {!! nl2br(e($comment->content_html)) !!}
            @if ($comment->censored == true)</em>@endif
        </div>
    </div>
    @if ($comment->childrens->isNotEmpty())
        @foreach ($comment->childrens as $children)
            <div id="comment{{ $children->id }}" class="transition my-3 {{ ($children->real_depth < 5) ? 'depth-' . $children->real_depth : 'depth-5' }}">
                <div class="d-flex mb-2">
                    <small class="mr-auto">{{ trans('icore::comments.created_at_diff') }}: {{ $children->created_at_diff }}</small>
                    <small class="ml-auto">{{ trans('icore::comments.author') }}: {{ optional($children->user)->name }}</small>
                </div>
                <div>
                    @if ($children->censored == true)<em>@endif
                    {!! nl2br(e($children->content_html)) !!}
                    @if ($children->censored == true)</em>@endif
                </div>
            </div>
        @endforeach
    @endif
</div>
