@if ($archives->isNotEmpty())
<h3 class="h5">{{ trans('icore::archives.archive') }}</h3>
<ul class="list-group list-group-flush mb-3">
    @foreach ($archives as $archive)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="{{ route('web.archive.post.show', ['month' => $archive->month, 'year' => $archive->year]) }}"
        class="@isUrl(route('web.archive.post.show', ['month' => $archive->month, 'year' => $archive->year]), 'font-weight-bold')">
            {{ $archive->month_localized }} {{ $archive->year }}
        </a>
        <span class="badge badge-primary badge-pill">{{ $archive->posts_count }}</span>
    </li>
    @endforeach
</ul>
@endif
