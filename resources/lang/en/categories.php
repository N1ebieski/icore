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
    'names_json' => [
        'label' => 'Category names in JSON or TXT format',
        'tooltip' => 'For TXT format, categories type from a new line'
    ],
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
