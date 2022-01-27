<?php

use N1ebieski\ICore\Models\User;

return [
    'symlink' => 'symlinks',
    'success' => [
        'store' => 'The user has been added.',
        'destroy_global' => 'Successfully deleted :affected users.'
    ],
    'route' => [
        'index' => 'Users',
        'create' => 'Add user',
        'edit' => 'Edit user'
    ],
    'status' => [
        User::ACTIVE => 'active',
        User::INACTIVE => 'inactive'
    ],
    'roles' => 'Account type'
];
