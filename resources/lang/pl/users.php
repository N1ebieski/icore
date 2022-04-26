<?php

use N1ebieski\ICore\ValueObjects\User\Status;

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
        Status::ACTIVE => 'aktywne',
        Status::INACTIVE => 'nieaktywne'
    ],
    'roles' => 'Typ konta'
];
