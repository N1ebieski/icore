<div id="row{{ $page->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $page->id }}">
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.pages.delete')
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
                    {{ str_repeat('-', $page->real_depth) }}
                    <a href="#" class="edit" data-route="{{ route('admin.page.edit_position', [$page->id]) }}"
                    data-toggle="modal" data-target="#editPositionModal" role="button">
                        <span id="position" class="badge badge-pill badge-primary">{{ $page->real_position }}</span>
                    </a>
                    <a href="{{ route('admin.page.index', ['filter[parent]' => $page->id]) }}"
                    title=" {{ $page->title }}">
                        {{ $page->title }}
                    </a>&nbsp;
                    <span class="badge badge-success">ID {{ $page->id }}</span>
                </li>
                <li>{{ str_repeat('-', $page->real_depth) }} {{ $page->shortContent }}...</li>
                <li>{{ str_repeat('-', $page->real_depth) }} <small>{{ trans('icore::filter.created_at') }}: {{ $page->created_at_diff }}</small></li>
                <li>{{ str_repeat('-', $page->real_depth) }} <small>{{ trans('icore::filter.updated_at') }}: {{ $page->updated_at_diff }}</small></li>
            </ul>
        @can('admin.pages.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.pages.edit')
                <div class="btn-group-vertical">
                    <button data-toggle="modal" data-target="#editModal"
                    data-route="{{ route('admin.page.edit', ['page' => $page->id]) }}"
                    type="button" class="btn btn-primary edit">
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                    </button>
                    <a class="btn btn-primary align-bottom" href="{{ route('admin.page.edit_full', ['page' => $page->id]) }}"
                    role="button" target="_blank" rel="noreferrer noopener">
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.editFull') }}</span>
                    </a>
                </div>
                @endcan
                @can('admin.pages.status')
                <button data-status="1" type="button" class="btn btn-success statusPage"
                data-route="{{ route('admin.page.update_status', ['page' => $page->id]) }}"
                {{ $page->status == $page::ACTIVE ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button data-status="0" type="button" class="btn btn-warning statusPage"
                data-route="{{ route('admin.page.update_status', ['page' => $page->id]) }}"
                {{ $page->status == $page::INACTIVE ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('admin.pages.delete')
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('admin.page.destroy', ['page' => $page->id]) }}" data-id="{{ $page->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
                data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroyPage" 
                data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                data-btn-cancel-icon-class="fas fa-ban mr-1"
                data-title="{{ trans('icore::pages.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
