<?php

return [
    'post' => [
        'post' => 'Posts'
    ],
    'success' => [
        'store' => 'The category has been added',
        'store_global' => 'The category tree has been added',
        'store_parent' => ' to the parent category :parent',
        'destroy_global' => 'Successfully deleted :affected categories together with subcategories.'
    ],
    'error' => [
        'search' => 'No category was found.'
    ],
    'route' => [
        'index' => 'Categories',
        'edit' => 'Edit category',
        'create' => 'Add category',
        'edit_position' => 'Edit position',
        'show' => 'Category: :category'
    ],
    'name' => 'Name',
    'names_json' => 'Category names in JSON format',
    'clear' => 'Delete all existing categories before import?',
    'parent_id' => 'Parent category',
    'null' => 'None',
    'ancestors' => 'The category and subcategories belong to',
    'confirm' => 'Are you sure you want to delete categories with all their subcategories?',
    'create' => 'Add category',
    'categories' => [
        'label' => 'Categories',
        'tooltip' => 'Min 1 category, max :max_categories categories'
    ],
    'search_categories' => 'Search category [min 3 chars]',
    'position' => 'Position',
    'roots' => 'main',
    'icon' => [
        'label' => 'Icon class',
        'tooltip' => 'Icon class (for eaxample from font-awesome). The icon will be displayed next to the name.',
        'placeholder' => 'example font-awesome: fab fa-google'
    ]
];
