<h5 class="mb-2">
    {{ trans('icore::stats.stats') }}:
</h5>
<div class="list-group list-group-flush text-left">
    @if ($countCategories->firstWhere('status', $category::ACTIVE))
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::categories.route.index') }}:
        </div>
        <div class="text-right">
            {{ $countCategories->firstWhere('status', $category::ACTIVE)->count_rows }}
        </div>
    </div>
    @endif
    @if ($countPosts->firstWhere('status', $post::ACTIVE))
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::posts.route.index') }}:
        </div>
        <div class="text-right">
            {{ $countPosts->firstWhere('status', $post::ACTIVE)->count_rows }}
        </div>
    </div>
    @endif
    @if ($lastActivity)
    <div class="list-group-item d-flex justify-content-between">
        <div>
            {{ trans('icore::stats.last_activity') }}:
        </div>
        <div class="text-right">
            {{ now()->parse($lastActivity)->diffForHumans() }}
        </div>
    </div>
    @endif
    @if ($countUsers)
    <div class="list-group-item">
        <div>
            {{ trans('icore::stats.user.label') }}:
        </div>
        @foreach ($countUsers as $count)
        <div class="d-flex justify-content-between">
            <div>
                - {{ trans("icore::stats.user.{$count->type}.label") }}:
            </div>
            <div class="text-right">
                {{ $count->count_rows }}
            </div>
        </div>
        @endforeach
    </div>
    @endif     
</div>