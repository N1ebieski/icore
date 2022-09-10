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
