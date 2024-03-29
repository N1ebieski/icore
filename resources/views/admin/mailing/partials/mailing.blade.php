<div 
    id="row{{ $mailing->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $mailing->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.mailings.delete')
        <div class="custom-control custom-checkbox flex-grow-1">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select"
                id="select{{ $mailing->id }}" 
                value="{{ $mailing->id }}"
            >
            <label class="custom-control-label w-100" for="select{{ $mailing->id }}">
        @endcan
                <ul class="list-unstyled mb-0 pb-0 flex-grow-1">
                    <li class="mb-3 mt-1">
                        <div class="progress">
                            <div 
                                class="progress-bar bg-success progress-bar-striped {{ ($mailing->status->isInprogress()) ? 'progress-bar-animated' : '' }}"
                                role="progressbar" 
                                style="width: {{ $mailing->progress_success }}%"
                                aria-valuenow="{{ $mailing->progress_success }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100"
                            ></div>
                            <div 
                                class="progress-bar bg-danger progress-bar-striped {{ ($mailing->status->isInprogress()) ? 'progress-bar-animated' : '' }}"
                                role="progressbar" 
                                style="width: {{ $mailing->progress_failed }}%"
                                aria-valuenow="{{ $mailing->progress_failed }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100"
                            ></div>
                        </div>
                    </li>
                    <li>
                        <a 
                            href="{{ route('admin.mailing.edit', [$mailing->id]) }}" 
                            target="_blank" 
                            title="{{ $mailing->title }}" 
                            rel="noopener"
                        >
                            {{ $mailing->title }}
                        </a>
                    </li>
                    <li>
                        {{ $mailing->shortContent }}...
                    </li>
                    @if ($mailing->activation_at_diff)
                    <li>
                        <small>{{ trans('icore::mailings.activation_at.label') }}: {{ $mailing->activation_at_diff }}</small>
                    </li>
                    @endif
                    <li>
                        <small>{{ trans('icore::filter.created_at') }}: {{ $mailing->created_at_diff }}</small>
                    </li>
                    <li>
                        <small>{{ trans('icore::filter.updated_at') }}: {{ $mailing->updated_at_diff }}</small>
                    </li>
                </ul>
        @can('admin.mailings.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.mailings.edit')
                <a 
                    class="btn btn-primary align-bottom {{ $mailing->status->isRunning() ? 'disabled' : '' }}"
                    href="{{ route('admin.mailing.edit', [$mailing->id]) }}" 
                    role="button" 
                    target="_blank"
                    rel="noopener"
                >
                    <i class="far fa-edit"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                </a>
                @endcan
                @can('admin.mailings.status')
                <button 
                    data-status="1" 
                    type="button" 
                    class="btn btn-success status"
                    data-route="{{ route('admin.mailing.update_status', [$mailing->id]) }}"
                    {{ $mailing->status->isRunning() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button 
                    data-status="0" 
                    type="button" 
                    class="btn btn-warning status"
                    data-route="{{ route('admin.mailing.update_status', [$mailing->id]) }}"
                    {{ $mailing->status->isInactive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('admin.mailings.delete')
                <div class="btn-group-vertical justify-content-start">
                    <button 
                        type="button"                     
                        class="btn btn-danger" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.mailing.destroy', [$mailing->id]) }}" 
                        data-id="{{ $mailing->id }}"
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::default.confirm') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                    </button>
                    @if ($mailing->hasAdditionalLangs())
                    <button 
                        type="button"                
                        class="btn btn-danger" 
                        data-status="delete-lang" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.mailing_lang.destroy', ['mailingLang' => $mailing->currentLang->id]) }}" 
                        data-id="{{ $mailing->id }}"
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
                    @if ($mailing->emails_count > 0)
                    <button 
                        type="button"                     
                        class="btn btn-danger"
                        data-status="reset-mailing" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.mailing.reset', [$mailing->id]) }}" 
                        data-id="{{ $mailing->id }}"
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover reset-mailing" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::mailings.confirm') }}"
                    >
                        <i class="fas fa-power-off"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::mailings.reset') }}</span>
                    </button>
                    @endif
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
