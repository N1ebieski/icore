<?php

return [
    'install' => 'Uwaga: instalator zainstaluje nową instancję aplikacji.',
    'confirm' => 'Czy chcesz kontynuować?',
    'validate' => [
        'url' => 'Sprawdzanie poprawności adresu URL...',
        'license' => 'Sprawdzanie zgodności klucza licencji...',
        'connection_mail' => 'Sprawdzanie połączenia z serwerem poczty SMTP...',
        'connection_database' => 'Sprawdzanie połączenia z serwerem bazy danych...'
    ],
    'publish' => [
        'langs' => 'Kopiowanie plików lokalizacji językowej...',
        'migrations' => 'Kopiowanie migracji...',
        'factories' => 'Kopiowanie faktorek...',
        'seeds' => 'Kopiowanie seederów...',
        'views' => 'Kopiowanie plików widoku...',
        'js' => 'Kopiowanie assetów javascript...',
        'sass' => 'Kopiowanie assetów sass...',
        'public' => 'Kopiowanie zawartości folderu public...',
        'config' => 'Kopiowanie plików konfiguracyjnych...',
        'vendor' => 'Kopiowanie assetów vendor...'
    ],
    'migrate' => 'Tworzenie bazy danych...',
    'seed' => 'Seedowanie danych...',
    'dump' => 'Generowanie composer autoload...',
    'cache' => [
        'routes' => 'Generowanie cache routów...',
        'config' => 'Generowanie cache konfiguracji...'
    ],
    'register_superadmin' => 'Rejestrowanie super admina...'
];
