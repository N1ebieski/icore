<?php

use N1ebieski\ICore\Models\Comment\Comment;

return [
    'post' => [
        'post' => 'Posts'
    ],
    'page' => [
        'page' => 'Pages'
    ],
    'success' => [
        'store' => [
            Comment::INACTIVE => 'A comment has been added and awaits approval.'
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
