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
    'success' => [
        'store' => 'The link was created.'
    ],
    'route' => [
        'index' => 'Links',
        'create' => 'Add link',
        'edit' => 'Edit link',
        'edit_position' => 'Edit position'
    ],
    'link' => [
        'route' => [
            'index' => 'Links'
        ],
    ],
    'backlink' => [
        'route' => [
            'index' => 'Backlinks'
        ]
    ],
    'name' => 'Name',
    'url' => 'URL',
    'img' => 'Banner',
    'delete_img' => 'Remove the banner?',
    'only' => [
        'home' => 'Only homepage',
        'categories' => 'Only categories'
    ]
];
