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

use N1ebieski\ICore\ValueObjects\Comment\Status;

return [
    'post' => [
        'post' => 'Posts'
    ],
    'page' => [
        'page' => 'Pages'
    ],
    'success' => [
        'store' => [
            Status::INACTIVE => 'A comment has been added and awaits approval.'
        ],
        'destroy_global' => 'Successfully deleted :affected comments along with answers.',
    ],
    'route' => [
        'index' => 'Comments',
        'edit' => 'Edit comment',
        'create' => 'Add comment',
        'show_disqus' => 'Discussion'
    ],
    'content' => 'Content',
    'parent_id' => 'Parent comment',
    'null' => 'None',
    'confirm' => 'Are you sure you want to delete a comment with all their answers?',
    'create' => 'Dodaj komentarz',
    'created_at_diff' => 'Added',
    'author' => 'Author',
    'disqus' => 'Discussion on the subject :name',
    'reports' => 'Reports',
    'published_at' => 'Published at',
    'censored' => 'Comment deleted.',
    'answer' => 'Answer',
    'edit' => 'Edit',
    'report' => 'Report',
    'log_to_answer' => 'Log in to answer',
    'log_to_comment' => 'Log in to comment on',
    'next_answers' => 'Load the answers',
    'next_comments' => 'Load next comments',
    'comments' => 'Comments',
    'latest' => 'Recently commented'
];
