<div id="row{{ $category->id }}" class="row border-bottom py-3 position-relative transition"
    data-id="{{ $category->id }}">
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.categories.delete')
        <div class="custom-control custom-checkbox">
            <input name="select[]" type="checkbox" class="custom-control-input select" id="select{{ $category->id }}" value="{{ $category->id }}">
            <label class="custom-control-label" for="select{{ $category->id }}">
        @endcan
            <ul class="list-unstyled mb-0 pb-0">
                @if ($category->relationLoaded('ancestors') && $category->ancestors->isNotEmpty())
                    <li><small>{{ trans('icore::categories.ancestors') }}:
                        @foreach ($category->ancestors as $ancestor)
                            {{ $ancestor->name }}
                            @if (!$loop->last)
                                &raquo;
                            @endif
                        @endforeach
                    </small></li>
                @endif
                <li>
                    {{ str_repeat('-', $category->real_depth) }}&nbsp;
                    <a href="#" class="edit" data-route="{{ route('admin.category.edit_position', [$category->id]) }}"
                    data-toggle="modal" data-target="#editPositionModal" role="button">
                        <span id="position" class="badge badge-pill badge-primary">{{ $category->real_position }}</span>
                    </a>&nbsp;
                    <a href="{{ route("admin.category.{$category->poli}.index", ['filter[parent]' => $category->id]) }}"
                    title="{{ $category->name }}">
                        {{ $category->name }}
                    </a>
                </li>
                <li>{{ str_repeat('-', $category->real_depth) }} <small>{{ trans('icore::filter.created_at') }}: {{ $category->created_at_diff }}</small></li>
                <li>{{ str_repeat('-', $category->real_depth) }} <small>{{ trans('icore::filter.updated_at') }}: {{ $category->updated_at_diff }}</small></li>
            </ul>
        @can('admin.categories.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.categories.edit')
                <div class="btn-group-vertical">
                    <button data-toggle="modal" data-target="#editModal"
                    data-route="{{ route('admin.category.edit', ['category' => $category->id]) }}"
                    type="button" class="btn btn-primary edit">
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                    </button>
                </div>
                @endcan
                @can('admin.categories.status')
                <button data-status="1" type="button" class="btn btn-success statusCategory"
                data-route="{{ route('admin.category.update_status', ['category' => $category->id]) }}"
                {{ $category->status == 1 ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button data-status="0" type="button" class="btn btn-warning statusCategory"
                data-route="{{ route('admin.category.update_status', ['category' => $category->id]) }}"
                {{ $category->status == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('admin.categories.delete')
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('admin.category.destroy', ['category' => $category->id]) }}" data-id="{{ $category->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
                data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroyCategory" 
                data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                data-btn-cancel-icon-class="fas fa-ban mr-1"
                data-title="{{ trans('icore::categories.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
