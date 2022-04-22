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
                'views' => [
                    'vendor/icore/web',
                    'vendor/icore/mails'
                ],
                'lang' => [
                    'vendor/icore'
                ]
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
                ]
            ]
        ],
        [
            'paths' => [
                'views' => [
                    'vendor/icore/web'
                ]
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\(bool\)\$post->seo_noindex\s*===\s*true/',
                    'to' => '$post->seo_noindex->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$post->seo_noindex\s*===\s*true/',
                    'to' => '$post->seo_noindex->isActive()'
                ]
            ]
        ],
        [
            'paths' => [
                'lang' => [
                    'vendor/icore'
                ]
            ],
            'actions' => [
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\ICore\\\Models\\\Post;/',
                    'to' => 'use N1ebieski\ICore\ValueObjects\Post\Status;'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => "/use\s*N1ebieski\\\ICore\\\Models\\\Post;\\n*/"
                ]
            ]
        ]
    ];
}
