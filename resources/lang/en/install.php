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
    'install' => 'Note: The installer will install a new instance of the application.',
    'confirm' => 'Do you want to continue?',
    'validate' => [
        'url' => 'URL Validation...',
        'connection_mail' => 'Checking the connection to the SMTP mail server...',
        'connection_database' => 'Checking the connection to the database server...',
        'license' => 'Validating License Key...'
    ],
    'publish' => [
        'langs' => 'Copying language location files...',
        'migrations' => 'Copying migration...',
        'factories' => 'Copying factories...',
        'seeders' => 'Copying seeders...',
        'views' => 'Copying view files...',
        'js' => 'Copying javascript assets...',
        'sass' => 'Copying sass assets...',
        'public' => 'Copying public folder with files...',
        'config' => 'Copying config files...',
        'vendor' => 'Copying vendor assets...'
    ],
    'migrate' => 'Creating a database...',
    'seed' => 'Seeding data...',
    'dump' => 'Generating composer autoload...',
    'cache' => [
        'routes' => 'Generating cache routes...',
        'config' => 'Generating cache configs...'
    ],
    'storage_link' => 'Creating a symlink to the folder storage...',
    'register_superadmin' => 'Registering super admin...'
];
