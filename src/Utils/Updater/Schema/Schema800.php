<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Utils\Updater\Schema;

use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Schema800 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/icore/web/profile/edit.blade.php',
                'resources/views/vendor/icore/auth/login.blade.php',
                'resources/views/vendor/icore/auth/register.blade.php',
                'resources/views/vendor/icore/admin/page/create.blade.php',
                'resources/views/vendor/icore/admin/page/edit_full.blade.php',
                'resources/views/vendor/icore/admin/post/create.blade.php',
                'resources/views/vendor/icore/admin/post/edit_full.blade.php',
                'resources/views/vendor/icore/web/components/newsletter.blade.php',
                'resources/views/vendor/icore/web/contact/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/custom-checkbox/',
                    'to' => 'custom-switch'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/page/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/<h1.*?>/',
                    'to' => '<div class="d-flex justify-content-between">'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/<\/h1>/',
                    'to' => <<<EOD
                    @can ('admin.pages.view')
                    <div>
                        <a
                            href="{{ route('admin.page.index', ['filter[search]' => 'id:"' . \$page->id . '"']) }}"
                            target="_blank"
                            rel="noopener"
                            title="{{ trans('icore::pages.route.index') }}"
                            class="badge badge-primary"
                        >
                            {{ trans('icore::default.admin') }}
                        </a>
                    </div>
                    @endcan
                </div>                    
EOD
                ],
                [
                    'type' => 'afterLast',
                    'search' => '/@endsection/',
                    'to' => <<<EOD

@if (!empty(config('icore.captcha.driver')))
@php
app(\N1ebieski\ICore\View\Components\CaptchaComponent::class)->render()->render();
@endphp
@endif
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/post/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/<h1.*?>/',
                    'to' => '<div class="d-flex justify-content-between">'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/<\/h1>/',
                    'to' => <<<EOD
                    @can ('admin.posts.view')
                    <div>
                        <a
                            href="{{ route('admin.post.index', ['filter[search]' => 'id:"' . \$post->id . '"']) }}"
                            target="_blank"
                            rel="noopener"
                            title="{{ trans('icore::posts.route.index') }}"
                            class="badge badge-primary"
                        >
                            {{ trans('icore::default.admin') }}
                        </a>
                    </div>
                    @endcan
                </div>                    
EOD
                ],
                [
                    'type' => 'afterLast',
                    'search' => '/@endsection/',
                    'to' => <<<EOD

@if (!empty(config('icore.captcha.driver')))
@php
app(\N1ebieski\ICore\View\Components\CaptchaComponent::class)->render()->render();
@endphp
@endif                    
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/post/partials/post.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/<h2.*?>/',
                    'to' => '<div class="d-flex justify-content-between">'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/<\/h2>/',
                    'to' => <<<EOD
                    @can ('admin.posts.view')
                    <div>
                        <a
                            href="{{ route('admin.post.index', ['filter[search]' => 'id:"' . \$post->id . '"']) }}"
                            target="_blank"
                            rel="noopener"
                            title="{{ trans('icore::posts.route.index') }}"
                            class="badge badge-primary"
                        >
                            {{ trans('icore::default.admin') }}
                        </a>
                    </div>
                    @endcan
                </div>                    
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/comment/partials/comment.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replaceMatches',
                    'search' => '/@auth([^}]*?<a[^>]*?data-route="{{\s*route\(\'web\.report\.comment\.create\',\s*\[\$comment->id\]\)\s*}}"[\s\S]*?)@endauth/',
                    'to' => '$1'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web/report/create.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/<\/form>\s*@endslot/',
                    'to' => <<<EOD
    <x-icore::captcha-component    
        id="1000"
    />
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'routes/vendor/icore/web/reports.php'
            ],
            'actions' => [
                [
                    'type' => 'remove',
                    'search' => '/Route::group\(\[\'middleware\'\s*=>\s*\'auth\'\],\s*function\s*\(\)\s*{/',
                ],
                [
                    'type' => 'remove',
                    'search' => '/}\);/',
                ]
            ]
        ]
    ];
}
