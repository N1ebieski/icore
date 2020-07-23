<?php

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
        'seeds' => 'Copying seeders...',
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
