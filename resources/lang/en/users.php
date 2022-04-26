<?php

use N1ebieski\ICore\ValueObjects\User\Status;

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
        Status::ACTIVE => 'active',
        Status::INACTIVE => 'inactive'
    ],
    'roles' => 'Account type'
];
