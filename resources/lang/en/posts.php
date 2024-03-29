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

use N1ebieski\ICore\ValueObjects\Post\Status;

return [
    'success' => [
        'store' => 'The post has been added.',
        'update' => 'The post has been changed.',
        'destroy_global' => 'Successfully deleted :affected posts.'
    ],
    'route' => [
        'blog' => 'Blog',
        'index' => 'Posts',
        'edit' => 'Edit post',
        'create' => 'Add post',
        'search' => 'Search: :search'
    ],
    'title' => 'Title',
    'content' => 'Content',
    'published_at_diff' => 'Published at',
    'author' => 'Author',
    'published_at' => [
        'label' => 'Published at',
        'tooltip' => 'If the date is future, publication will be deferred until then'
    ],
    'tags' => [
        'label' => 'Tags',
        'tooltip' => 'Min 3 chars, max :max_chars chars, max :max_tags tags',
        'placeholder' => 'Add tags'
    ],
    'seo' => [
        'tooltip' => 'Used in META and Open Graph',
    ],
    'draft' => 'draft',
    'create' => 'Add post',
    'more' => 'more &raquo',
    'comment' => 'Comments enabled',
    'status' => [
        Status::ACTIVE => 'active',
        Status::INACTIVE => 'inactive',
        Status::SCHEDULED => 'scheduled',
    ],
    'related' => 'Related posts',
    'chart' => [
        'x' => [
            'label' => 'Data'
        ],
        'y' => [
            'label' => 'Number of content'
        ],
        'count_by_date' => 'Chart of number of posts and pages on timeline'
    ]
];
