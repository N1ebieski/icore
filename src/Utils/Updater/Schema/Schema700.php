<?php

namespace N1ebieski\ICore\Utils\Updater\Schema;

use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Schema700 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/icore/web/partials/nav.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/<a[^<]*?route\(\'logout\'\)[\s\S]*?<\/a>/',
                    'to' => <<<EOD
<form 
                            class="d-inline" 
                            method="POST" 
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button type="submit" class="btn btn-link dropdown-item">
                                {{ trans('icore::auth.route.logout') }}
                            </button>
                        </form>
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/icore/web'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$post->seo_noindex\s*={2,3}\s*true/',
                    'to' => '$post->seo_noindex->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$post->seo_noindex\s*={2,3}\s*true/',
                    'to' => '$post->seo_noindex->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$post->seo_nofollow\s*={2,3}\s*true/',
                    'to' => '$post->seo_nofollow->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$post->seo_nofollow\s*={2,3}\s*true/',
                    'to' => '$post->seo_nofollow->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$post->comment\s*={2,3}\s*true/',
                    'to' => '$post->comment->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$post->comment\s*={2,3}\s*true/',
                    'to' => '$post->comment->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/old\(\'marketing_agreement\',\s*\$user->marketing\)\s*={2,3}\s*true/',
                    'to' => 'old(\'marketing_agreement\', $user->marketing->getValue()) == User\Marketing::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$page->seo_noindex\s*={2,3}\s*true/',
                    'to' => '$page->seo_noindex->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$page->seo_noindex\s*={2,3}\s*true/',
                    'to' => '$page->seo_noindex->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$page->seo_nofollow\s*={2,3}\s*true/',
                    'to' => '$page->seo_nofollow->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$page->seo_nofollow\s*={2,3}\s*true/',
                    'to' => '$page->seo_nofollow->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$page->comment\s*={2,3}\s*true/',
                    'to' => '$page->comment->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$page->comment\s*={2,3}\s*true/',
                    'to' => '$page->comment->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$comment->censored\s*={2,3}\s*true/',
                    'to' => '$comment->censored->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$comment->censored\s*={2,3}\s*true/',
                    'to' => '$comment->censored->isActive()'
                ]
            ]
            ],
            [
            'paths' => [
                'resources/lang/vendor/icore'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/Post::ACTIVE/',
                    'to' => 'Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Post::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Post::SCHEDULED/',
                    'to' => 'Status::SCHEDULED'
                ],
                [
                    'type' => 'replace',
                    'search' => '/User::ACTIVE/',
                    'to' => 'Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/User::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Newsletter::ACTIVE/',
                    'to' => 'Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Newsletter::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Mailing::ACTIVE/',
                    'to' => 'Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Mailing::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Mailing::SCHEDULED/',
                    'to' => 'Status::SCHEDULED'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Mailing::INPROGRESS/',
                    'to' => 'Status::INPROGRESS'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Stat::CLICK/',
                    'to' => 'Slug::CLICK'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Stat::VIEW/',
                    'to' => 'Slug::VIEW'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Page::ACTIVE/',
                    'to' => 'Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Page::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Comment::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Post;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Post\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\User;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\User\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Newsletter;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Newsletter\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Mailing;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Mailing\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Stat\\\Stat;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Stat\Slug;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Page\\\Page;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Page\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Comment\\\Comment;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Comment\Status;'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Post;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\User;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Newsletter;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Mailing;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Stat\\\Stat;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Page\\\Page;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Comment\\\Comment;\\n*/'
                ]
            ]
        ]
    ];
}
