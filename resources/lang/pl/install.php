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
    'install' => 'Uwaga: instalator zainstaluje nową instancję aplikacji.',
    'confirm' => 'Czy chcesz kontynuować?',
    'validate' => [
        'url' => 'Sprawdzanie poprawności adresu URL...',
        'connection_mail' => 'Sprawdzanie połączenia z serwerem poczty SMTP...',
        'connection_database' => 'Sprawdzanie połączenia z serwerem bazy danych...',
        'license' => 'Sprawdzanie poprawności klucza licencji...'
    ],
    'publish' => [
        'langs' => 'Kopiowanie plików lokalizacji językowej...',
        'migrations' => 'Kopiowanie migracji...',
        'factories' => 'Kopiowanie faktorek...',
        'seeders' => 'Kopiowanie seederów...',
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
    'storage_link' => 'Tworzenie symlinka do folderu storage...',
    'register_superadmin' => 'Rejestrowanie super admina...'
];
