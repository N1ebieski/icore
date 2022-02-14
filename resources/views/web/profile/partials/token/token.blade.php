<div 
    class="row border-bottom py-3 position-relative transition"
    id="row{{ $token->id }}"
    data-id="{{ $token->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>{{ $token->name }}</li>
            <li>{{ implode(',', $token->abilities) }}</li>
            <li>
                <small>
                    {{ trans('icore::tokens.expired_at') }}:
                </small>
                <small class="{{ optional($token->expired_at)->lt(now()) ? 'text-danger' : null }}">
                    {{ $token->expired_at !== null ? $token->expired_at_diff : trans('icore::tokens.unlimited') }}
                </small>
            </li>
            <li>
                <small>
                    {{ trans('icore::filter.created_at') }}: {{ $token->created_at_diff }}
                </small>
            </li>
            <li>
                <small>
                    {{ trans('icore::filter.updated_at') }}: {{ $token->updated_at_diff }}
                </small>
            </li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('web.tokens.delete')
                @can('api.access')
                <button 
                    class="btn btn-danger" 
                    data-status="delete" 
                    data-toggle="confirmation"
                    data-route="{{ route('web.token.destroy', [$token->id]) }}" 
                    data-id="{{ $token->id }}"
                    type="button" 
                    data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                    data-btn-ok-icon-class="fas fa-check mr-1"
                    data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                    data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                    data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                    data-btn-cancel-icon-class="fas fa-ban mr-1"
                    data-title="{{ trans('icore::default.confirm') }}"
                >
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-md-inline">
                        {{ trans('icore::default.delete') }}
                    </span>
                </button>
                @endcan
                @endcan
            </div>
        </div>
    </div>
</div>
