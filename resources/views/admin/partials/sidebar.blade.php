<div class="sidebar position scroll {{ $isCookie('sidebar_toggle', 'toggled') }}">
    <ul 
        class="sidebar bg-light navbar-light position-fixed scroll navbar-nav border-right {{ $isCookie('sidebar_toggle', 'toggled') }}"
    >
        <li class="nav-item navbar-light fake-toggler">
            <a 
                href="#" 
                class="navbar-toggler" 
                role="button" 
                id="sidebar-toggle"
            >
                <span class="navbar-toggler-icon"></span>
            </a>
        </li>
        @can('admin.home.view')
        <li class="nav-item {{ $isUrl(route('admin.home.index')) }}">
            <a 
                class="nav-link" 
                href="{{ route('admin.home.index') }}" 
                title="Dashboard"
            >
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span> Dashboard</span>
            </a>
        </li>
        @endcan
        @can('admin.pages.view')
        <li class="nav-item {{ $isUrlContains(['*/pages', '*/pages/*']) }}">
            <a 
                class="nav-link" 
                href="{{ route('admin.page.index') }}" 
                title="{{ trans('icore::pages.route.index') }}"
            >
                <i class="fas fa-fw fa-file-word"></i>
                <span> {{ trans('icore::pages.route.index') }}</span>
            </a>
        </li>
        @endcan
        @can('admin.posts.view')
        <li class="nav-item {{ $isUrlContains(['*/posts', '*/posts/*']) }}">
            <a 
                class="nav-link" 
                href="{{ route('admin.post.index') }}" 
                title="{{ trans('icore::posts.route.index') }}"
            >
                <i class="fas fa-fw fa-blog"></i>
                <span> {{ trans('icore::posts.route.index') }}</span>
            </a>
        </li>
        @endcan
        @can('admin.comments.view')
        <li class="nav-item dropdown {{ $isUrl([
            route('admin.comment.post.index'),
            route('admin.comment.page.index'),
        ]) }}">
            <a 
                class="nav-link dropdown-toggle" 
                href="#" 
                id="comment-dropdown" 
                role="button"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-fw fa-comments"></i>
                <span> {{ trans('icore::comments.route.index') }} </span>
                @if ($count = $commentsInactiveCount->sum('count'))
                <span class="badge badge-warning">{{ $count }}</span>
                @endif
                @if ($count = $commentsReportedCount->sum('count'))
                <span class="badge badge-danger">{{ $commentsReportedCount->sum('count') }}</span>
                @endif
            </a>
            <div class="dropdown-menu" aria-labelledby="comment-dropdown">
                <h6 class="dropdown-header">
                    {{ trans('icore::default.type') }}:
                </h6>
                @foreach(['post', 'page'] as $type)
                <div 
                    class="dropdown-item {{ $isUrl(route("admin.comment.{$type}.index")) }}"
                    onclick="window.location.href='{{ route("admin.comment.{$type}.index") }}'"
                    style="cursor: pointer;"
                >
                    {{ trans("icore::comments.{$type}.{$type}") }}
                    @if ($count = $commentsInactiveCount->where('model', $type)->first())
                    <span>
                        <a 
                            href="{{ route("admin.comment.{$type}.index", ['filter[status]' => Comment\Status::INACTIVE]) }}"
                            class="badge badge-warning"
                        >
                            {{ $count->count }}
                        </a>
                    </span>
                    @endif
                    @if ($count = $commentsReportedCount->where('model', $type)->first())
                    <span>
                        <a 
                            href="{{ route("admin.comment.{$type}.index", ['filter[report]' => Report\Reported::ACTIVE]) }}"
                            class="badge badge-danger"
                        >
                            {{ $count->count }}
                        </a>
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </li>
        @endcan
        @can('admin.categories.view')
        <li class="nav-item dropdown {{ $isUrl([route('admin.category.post.index')]) }}">
            <a 
                class="nav-link dropdown-toggle"
                href="#" 
                id="pages-dropdown" 
                role="button"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-fw fa-layer-group"></i>
                <span> {{ trans('icore::categories.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pages-dropdown">
                <h6 class="dropdown-header">
                    {{ trans('icore::default.type') }}:
                </h6>
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.category.post.index')) }}"
                    href="{{ route('admin.category.post.index') }}" 
                    title="{{ trans('icore::categories.post.post') }}"
                >
                    {{ trans('icore::categories.post.post') }}
                </a>
            </div>
        </li>
        @endcan
        @can('admin.mailings.view')
        <li class="nav-item {{ $isUrlContains(['*/mailings', '*/mailings/*']) }}">
            <a 
                class="nav-link" 
                href="{{ route('admin.mailing.index') }}"
                title="{{ trans('icore::mailings.route.index') }}"
            >
                <i class="fas fa-fw fa-envelope"></i>
                <span> {{ trans('icore::mailings.route.index') }}</span>
            </a>
        </li>
        @endcan
        @canany(['admin.users.view', 'admin.bans.view', 'admin.roles.view'])
        <li 
            class="nav-item dropdown {{ $isUrl([
                route('admin.user.index'),
                route('admin.role.index'),
                route('admin.banmodel.user.index'),
                route('admin.banvalue.index', [BanValue\Type::IP])
            ]) }} 
            {{ $isUrlContains(['*/roles', '*/roles/*']) }}"
        >
            <a 
                class="nav-link dropdown-toggle"
                href="#" 
                id="user-dropdown" 
                role="button"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-fw fa-users"></i>
                <span> {{ trans('icore::users.route.index') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="user-dropdown">
                @can('admin.users.view')
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.user.index')) }}"
                    href="{{ route('admin.user.index') }}" 
                    title="{{ trans('icore::users.route.index') }}"
                >
                    {{ trans('icore::users.route.index') }}
                </a>
                @endcan
                @can('admin.roles.view')
                <a 
                    class="dropdown-item {{ $isUrlContains(['*/roles', '*/roles/*']) }}"
                    href="{{ route('admin.role.index') }}" 
                    title="{{ trans('icore::roles.route.index') }}"
                >
                    {{ trans('icore::roles.route.index') }}
                </a>
                @endcan
                @can('admin.bans.view')
                <h6 class="dropdown-header">
                    {{ trans('icore::bans.route.index') }}:
                </h6>
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.banmodel.user.index')) }}"
                    href="{{ route('admin.banmodel.user.index') }}"
                    title="{{ trans('icore::bans.model.user.route.index') }}"
                >
                    {{ trans('icore::bans.model.user.route.index') }}
                </a>
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.banvalue.index', [BanValue\Type::IP])) }}"
                    href="{{ route('admin.banvalue.index', [BanValue\Type::IP]) }}"
                    title="{{ trans('icore::bans.value.ip.route.index') }}"
                >
                    {{ trans('icore::bans.value.ip.route.index') }}
                </a>
                @endcan
            </div>
        </li>
        @endcanany
        @can('admin.filemanager.read')
        <li class="nav-item {{ $isUrl(route('admin.filemanager.index')) }}">
            <a 
                class="nav-link" 
                href="{{ route('admin.filemanager.index') }}" 
                title="{{ trans('icore::filemanager.route.index') }}"
            >
                <i class="far fa-fw fa-image"></i>
                <span> {{ trans('icore::filemanager.route.index') }}</span>
            </a>
        </li>
        @endcan
        @canany(['admin.bans.view', 'admin.links.view', 'admin.tags.view'])
        <li class="nav-item dropdown {{ $isUrl([
            route('admin.banvalue.index', [BanValue\Type::WORD]),
            route('admin.link.index', [Link\Type::LINK]),
            route('admin.tag.index')
        ]) }}">
            <a 
                class="nav-link dropdown-toggle"
                href="#" 
                id="other-dropdown" 
                role="button"
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i class="fas fa-fw fa-tools"></i>
                <span> {{ trans('icore::admin.route.settings') }}</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="other-dropdown">
                @can('admin.bans.view')
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.banvalue.index', [BanValue\Type::WORD])) }}"
                    href="{{ route('admin.banvalue.index', [BanValue\Type::WORD]) }}"
                    title="{{ trans('icore::bans.value.word.route.index') }}"
                >
                    {{ trans('icore::bans.value.word.route.index') }}
                </a>
                @endcan
                @can('admin.links.view')
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.link.index', [Link\Type::LINK])) }}"
                    href="{{ route('admin.link.index', [Link\Type::LINK]) }}"
                    title="{{ trans('icore::links.link.route.index') }}"
                >
                    {{ trans('icore::links.link.route.index') }}
                </a>
                @endcan
                @can('admin.tags.view')
                <a 
                    class="dropdown-item {{ $isUrl(route('admin.tag.index')) }}"
                    href="{{ route('admin.tag.index') }}"
                    title="{{ trans('icore::tags.route.index') }}"
                >
                    {{ trans('icore::tags.route.index') }}
                </a>
                @endcan            
            </div>
        </li>
        @endcanany
    </ul>
</div>
