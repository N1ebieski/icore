<?php

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
