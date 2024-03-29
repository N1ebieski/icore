<div 
    id="row{{ $page->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $page->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.pages.delete')
        <div class="custom-control custom-checkbox w-100">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select" 
                id="select{{ $page->id }}" 
                value="{{ $page->id }}"
            >
            <label class="custom-control-label w-100" for="select{{ $page->id }}">
        @endcan
            <ul class="list-unstyled mb-0 pb-0">
                @if (
                    (!isset($filter) || !collect($filter)->except(['paginate', 'except', 'parent'])->isEmptyItems())
                    && $page->relationLoaded('ancestors')
                    && $page->ancestors->isNotEmpty()
                )
                <li>
                    <small>
                        <span>{{ trans('icore::pages.ancestors') }}:</span>
                        @foreach ($page->ancestors as $ancestor)
                            <span>{{ $ancestor->title ?? trans('icore::multi_langs.no_trans') }}</span>
                            @if (!$loop->last)
                            <span>&raquo;</span>
                            @endif
                        @endforeach
                    </small>
                </li>
                @endif
                <li>
                <div class="d-flex justify-content-between">
                    <div>
                        <span>{{ str_repeat('-', $page->real_depth) }}</span>
                        <span>
                            <a 
                                href="#" 
                                class="edit" 
                                data-route="{{ route('admin.page.edit_position', [$page->id]) }}"
                                data-toggle="modal" 
                                data-target="#edit-position-modal" 
                                role="button"
                            >
                                <span id="position" class="badge badge-pill badge-primary">
                                    {{ $page->real_position }}
                                </span>
                            </a>
                        </span>
                        <span>
                            <a 
                                href="{{ route('admin.page.index', ['filter[parent]' => $page->id]) }}"
                                title=" {{ $page->title }}"
                            >
                                {{ $page->title }}
                            </a>
                        </span>
                        <span class="badge badge-success">ID {{ $page->id }}</span>
                    </div>
                    @if ($page->status->isActive())
                    <div>
                        <a
                            href="{{ route('web.page.show', [$page->slug]) }}"
                            target="_blank"
                            rel="noopener"
                            title="{{ $page->title }}"
                            class="badge badge-primary"
                        >
                            {{ trans('icore::default.web') }}
                        </a>
                    </div>
                    @endif
                </div>                     
                </li>
                <li class="text-break" style="word-break:break-word">
                    {{ str_repeat('-', $page->real_depth) }} {!! $page->shortContent !!}...
                </li>
                <li>
                    <span>{{ str_repeat('-', $page->real_depth) }}</span>
                    <small>{{ trans('icore::filter.created_at') }}: {{ $page->created_at_diff }}</small>
                </li>
                <li>
                    <span>{{ str_repeat('-', $page->real_depth) }}</span>
                    <small>{{ trans('icore::filter.updated_at') }}: {{ $page->updated_at_diff }}</small>
                </li>
            </ul>
        @can('admin.pages.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.pages.edit')
                <div class="btn-group-vertical">
                    <button 
                        data-toggle="modal" 
                        data-target="#edit-modal"
                        data-route="{{ route('admin.page.edit', ['page' => $page->id]) }}"
                        type="button" 
                        class="btn btn-primary edit"
                    >
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                    </button>
                    <a 
                        class="btn btn-primary align-bottom" 
                        href="{{ route('admin.page.edit_full', ['page' => $page->id]) }}"
                        role="button" 
                        target="_blank" 
                        rel="noopener"
                    >
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.editFull') }}</span>
                    </a>
                </div>
                @endcan
                @can('admin.pages.status')
                <button 
                    data-status="{{ Page\Status::ACTIVE }}" 
                    type="button" 
                    class="btn btn-success status-page"
                    data-route="{{ route('admin.page.update_status', ['page' => $page->id]) }}"
                    {{ $page->status->isActive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button 
                    data-status="{{ Page\Status::INACTIVE }}" 
                    type="button" 
                    class="btn btn-warning status-page"
                    data-route="{{ route('admin.page.update_status', ['page' => $page->id]) }}"
                    {{ $page->status->isInactive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('admin.pages.delete')
                <div class="btn-group-vertical justify-content-start">                
                    <button 
                        type="button"                
                        class="btn btn-danger" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.page.destroy', ['page' => $page->id]) }}" 
                        data-id="{{ $page->id }}" 
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy-page" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::pages.confirm') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                    </button>
                    @if ($page->hasAdditionalLangs())
                    <button 
                        type="button"                
                        class="btn btn-danger" 
                        data-status="delete-lang" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.page_lang.destroy', ['pageLang' => $page->currentLang->id]) }}" 
                        data-id="{{ $page->id }}"
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy-lang" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::multi_langs.confirm') }}"
                    >
                        <i class="fas fa-trash"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::multi_langs.delete') }}</span>
                    </button>
                    @endif
                </div>                    
                @endcan
            </div>
        </div>
    </div>
</div>
