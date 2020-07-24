@if ($links->isNotEmpty())
<h5 class="mt-3 mb-2">{{ trans('icore::links.route.index') }}:</h5>
<ul class="list-group list-group-flush">
    @foreach ($links as $link)
    <li class="list-group-item">
        {!! $link->link_as_html !!}
    </li>
    @endforeach
</ul>
@endif
