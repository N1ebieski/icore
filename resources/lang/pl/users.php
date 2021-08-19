<?php

use N1ebieski\ICore\Models\User;

return [
    'symlink' => 'powiązane',
    'success' => [
        'store' => 'Użytkownik został dodany.',
        'destroy_global' => 'Pomyślnie usunięto :affected użytkowników.'
    ],
    'route' => [
        'index' => 'Użytkownicy',
        'create' => 'Dodaj użytkownika',
        'edit' => 'Edytuj użytkownika'
    ],
    'status' => [
        User::ACTIVE => 'aktywne',
        User::INACTIVE => 'nieaktywne'
    ],
    'roles' => 'Typ konta'
];
