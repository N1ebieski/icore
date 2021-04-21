<?php

use N1ebieski\ICore\Models\Page\Page;

return [
    'success' => [
        'store' => 'A page has been added',
        'store_parent' => ' to parent page :parent',
        'update' => 'A page has been changed',
        'destroy_global' => 'Successfully deleted :affected pages together with their subpages'
    ],
    'route' => [
        'index' => 'Pages',
        'edit' => 'Edit page',
        'edit_position' => 'Edit position',
        'create' => 'Add page'
    ],
    'title' => 'Title',
    'content' => 'Content',
    'parent_id' => 'Parent',
    'null' => 'None',
    'ancestors' => 'Page and subpages belong to parent',
    'confirm' => 'Are you sure you want to delete pages with all their subpages?',
    'create' => 'Add a page',
    'roots' => 'main',
    'comment' => 'Comments enabled',
    'seo' => [
        'tooltip' => 'Used in META and Open Graph'
    ],
    'tags' => [
        'label' => 'Tags',
        'tooltip' => 'Min 3 chars, max :max_chars chars, max :max_tags tags',
        'placeholder' => 'Add tags'
    ],
    'icon' => [
        'label' => 'Icon class',
        'tooltip' => 'Icon class (for eaxample from font-awesome). The icon will be displayed next to the title.',
        'placeholder' => 'example font-awesome: fab fa-google',
    ],
    'status' => [
        Page::ACTIVE => 'active',
        Page::INACTIVE => 'inactive'
    ],
    'position' => 'Position',
    'pages' => 'Pages',
    'map' => 'See also',
    'more' => 'more &raquo'    
];
