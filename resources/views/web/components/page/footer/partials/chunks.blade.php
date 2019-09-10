@if ($pattern)
    @foreach ($pattern as $chunk)
    <div class="col-md-3 col-sm-6">
        <div class="list-group list-group-flush mb-3">
            @include('icore::web.components.page.footer.partials.pages', ['pages' => $pages->whereIn('id', $chunk)->all()])
        </div>
    </div>
    @endforeach
@else
    @foreach ($pages->chunk($cols) as $chunk)
    <div class="col-md-3 col-sm-6">
        <div class="list-group list-group-flush mb-3">
            @include('icore::web.components.page.footer.partials.pages', ['pages' => $chunk])
        </div>
    </div>
    @endforeach
@endif
