@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::friends.route.index')],
    'desc' => [trans('icore::friends.route.index')],
    'keys' => [trans('icore::friends.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::friends.route.index') }}
</li>
@endsection

@section('content')
<div class="container">
    <h1 class="h4 border-bottom pb-2">
        {{ trans('icore::friends.friends') }}
    </h1>
    <ul>
        <li>
            <a href="https://intelekt.net.pl/icore" target="_blank" rel="noopener">
                iCore - mini platforma blogowa
            </a>
        </li>
        <li>
            <a href="https://www.iconpacks.net" target="_blank" rel="noopener">
                Iconpacks - completely free icons
            </a>
        </li>
    </ul>
</div>
@endsection
