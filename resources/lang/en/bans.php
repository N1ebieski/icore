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

use N1ebieski\ICore\ValueObjects\BanValue\Type;

return [
    'success' => [
        'destroy_global' => 'Successfully removed :affected banes from the blacklist.'
    ],
    'route' => [
        'index' => 'Bans',
        'create' => 'Add to the blacklist',
        'edit' => 'Edit ban'
    ],
    'value' => [
        'success' => [
            'store' => 'Successfully added value to the blacklist.'
        ],
        Type::IP => [
            'ip' => 'IP address',
            'route' => [
                'index' => 'Banned IP addresses'
            ],
        ],
        Type::WORD => [
            'route' => [
                'index' => 'Banned words'
            ]
        ],
        'create' => 'Add value',
        'value' => 'Value',
    ],
    'model' => [
        'success' => [
            'store' => 'Successfully added to the blacklist.'
        ],
        'user' => [
            'user' => 'User',
            'route' => [
                'index' => 'Banned users'
            ],
        ],
    ],
];
