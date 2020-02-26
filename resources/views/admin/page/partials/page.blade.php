<div id="row{{ $page->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $page->id }}">
    <div class="col my-auto d-flex justify-content-between">
        @can('destroy pages')
        <div class="custom-control custom-checkbox">
            <input name="select[]" type="checkbox" class="custom-control-input select" id="select{{ $page->id }}" value="{{ $page->id }}">
            <label class="custom-control-label" for="select{{ $page->id }}">
        @endcan
            <ul class="list-unstyled mb-0 pb-0">
                @if ($page->relationLoaded('ancestors') && $page->ancestors->isNotEmpty())
                    <li><small>{{ trans('icore::pages.ancestors') }}:
                        @foreach ($page->ancestors as $ancestor)
                            {{ $ancestor->title }}
                            @if (!$loop->last)
                                &raquo;
                            @endif
                        @endforeach
                    </small></li>
                @endif
                <li>
                    {{ str_repeat('-', $page->real_depth) }}&nbsp;
                    <a href="#" class="edit" data-route="{{ route('admin.page.edit_position', [$page->id]) }}"
                    data-toggle="modal" data-target="#editPositionModal" role="button">
                        <span id="position" class="badge badge-pill badge-primary">{{ $page->real_position }}</span>
                    </a>
                    &nbsp;<a href="{{ route('admin.page.index', ['filter[parent]' => $page->id]) }}">{{ $page->title }}</a>
                </li>
                <li>{{ str_repeat('-', $page->real_depth) }} {{ $page->shortContent }}...</li>
                <li>{{ str_repeat('-', $page->real_depth) }} <small>{{ trans('icore::filter.created_at') }}: {{ $page->created_at_diff }}</small></li>
                <li>{{ str_repeat('-', $page->real_depth) }} <small>{{ trans('icore::filter.updated_at') }}: {{ $page->updated_at_diff }}</small></li>
            </ul>
        @can('destroy pages')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('edit pages')
                <div class="btn-group-vertical">
                    <button data-toggle="modal" data-target="#editModal"
                    data-route="{{ route('admin.page.edit', ['page' => $page->id]) }}"
                    type="button" class="btn btn-primary edit">
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.edit') }}</span>
                    </button>
                    <a class="btn btn-primary align-bottom" href="{{ route('admin.page.edit_full', ['page' => $page->id]) }}"
                    role="button" target="_blank">
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.editFull') }}</span>
                    </a>
                </div>
                @endcan
                @can('status pages')
                <button data-status="1" type="button" class="btn btn-success statusPage"
                data-route="{{ route('admin.page.update_status', ['page' => $page->id]) }}"
                {{ $page->status == 1 ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.active') }}</span>
                </button>
                <button data-status="0" type="button" class="btn btn-warning statusPage"
                data-route="{{ route('admin.page.update_status', ['page' => $page->id]) }}"
                {{ $page->status == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('destroy pages')
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('admin.page.destroy', ['page' => $page->id]) }}" data-id="{{ $page->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
                data-btn-ok-class="btn-primary btn-popover destroyPage" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
                data-title="{{ trans('icore::pages.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>