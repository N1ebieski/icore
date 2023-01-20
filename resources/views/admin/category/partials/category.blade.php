<div 
    id="row{{ $category->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $category->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.categories.delete')
        <div class="custom-control custom-checkbox">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select" 
                id="select{{ $category->id }}" 
                value="{{ $category->id }}"
            >
            <label class="custom-control-label" for="select{{ $category->id }}">
        @endcan
            <ul class="list-unstyled mb-0 pb-0">
                @if ((!isset($filter) || !collect($filter)->except(['paginate', 'except', 'parent'])->isEmptyItems())
                && $category->relationLoaded('ancestors') && $category->ancestors->isNotEmpty())
                <li>
                    <small>
                        <span>{{ trans('icore::categories.ancestors') }}:</span>
                        @foreach ($category->ancestors as $ancestor)
                            <span>{{ $ancestor->name ?? trans('icore::multi_langs.no_trans') }}</span>
                            @if (!$loop->last)
                            <span>&raquo;</span>
                            @endif
                        @endforeach
                    </small>
                </li>
                @endif
                <li>
                    <span>{{ str_repeat('-', $category->real_depth) }}</span>
                    <span>
                        <a 
                            href="#" 
                            class="edit" 
                            data-route="{{ route('admin.category.edit_position', [$category->id]) }}"
                            data-toggle="modal" 
                            data-target="#edit-position-modal" 
                            role="button"
                        >
                            <span id="position" class="badge badge-pill badge-primary">
                                {{ $category->real_position }}
                            </span>
                        </a>
                    </span>
                    <span>
                        <a 
                            href="{{ route("admin.category.{$category->poli}.index", ['filter[parent]' => $category->id]) }}"
                            title="{{ $category->name }}"
                        >
                            {{ $category->name }}
                        </a>
                    </span>
                </li>
                <li>
                    <span>{{ str_repeat('-', $category->real_depth) }}</span>
                    <small>{{ trans('icore::filter.created_at') }}: {{ $category->created_at_diff }}</small>
                </li>
                <li>
                    <span>{{ str_repeat('-', $category->real_depth) }}</span>
                    <small>{{ trans('icore::filter.updated_at') }}: {{ $category->updated_at_diff }}</small>
                </li>
            </ul>
        @can('admin.categories.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.categories.edit')
                <button 
                    data-toggle="modal" 
                    data-target="#edit-modal"
                    data-route="{{ route('admin.category.edit', ['category' => $category->id]) }}"
                    type="button" 
                    class="btn btn-primary edit"
                >
                    <i class="far fa-edit"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                </button>
                @endcan
                @can('admin.categories.status')
                <button 
                    data-status="{{ Category\Status::ACTIVE }}" 
                    type="button" 
                    class="btn btn-success status-category"
                    data-route="{{ route('admin.category.update_status', ['category' => $category->id]) }}"
                    {{ $category->status->isActive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button 
                    data-status="{{ Category\Status::INACTIVE }}" 
                    type="button" 
                    class="btn btn-warning status-category"
                    data-route="{{ route('admin.category.update_status', ['category' => $category->id]) }}"
                    {{ $category->status->isInactive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('admin.categories.delete')
                <div class="btn-group-vertical">
                    <button 
                        type="button"                
                        class="btn btn-danger" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.category.destroy', ['category' => $category->id]) }}" 
                        data-id="{{ $category->id }}"
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy-category" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::categories.confirm') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                    </button>
                    @if ($category->hasAdditionalLangs())
                    <button 
                        type="button"                
                        class="btn btn-danger" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.category_lang.destroy', ['categoryLang' => $category->currentLang->id]) }}" 
                        data-id="{{ $category->id }}"
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
