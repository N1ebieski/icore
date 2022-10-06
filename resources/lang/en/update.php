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
    'update' => 'Note: The update will update the current application files.',
    'update' => 'Note: The update will restore the previous application files.',
    'confirm' => 'Do you want to continue?',
    'validate' => [
        'backup' => 'Checking an existing backup...',
    ],
    'errors' => [
        'backup' => [
            'exists' => 'There is already a backup for this version. You cannot re-update files that have already been updated once. Firstly restore the files to their previous state and delete the backup copy.',
            'no_exists' => 'There is no backup for this version.'
        ]
    ],
    'backup' => 'Creating a backup...',
    'update_files' => 'Updating files...',
    'rollback_files' => 'Restoring files...'
];
