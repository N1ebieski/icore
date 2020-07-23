<?php

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
        'ip' => [
            'ip' => 'IP address',
            'route' => [
                'index' => 'Banned IP addresses'
            ],
        ],
        'word' => [
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
