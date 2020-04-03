<?php

return [
    'success' => [
        'destroy_global' => 'Pomyślnie usunięto z czarnej listy :affected zbanowanych.'
    ],
    'route' => [
        'index' => 'Zbanowani',
        'create' => 'Dodaj do czarnej listy',
        'edit' => 'Edycja bana'
    ],
    'value' => [
        'success' => [
            'store' => 'Pomyślnie dodano wartość na czarną listę.'
        ],
        'ip' => [
            'ip' => 'Adres IP',
            'route' => [
                'index' => 'Zbanowane adresy IP'
            ],
        ],
        'word' => [
            'route' => [
                'index' => 'Zbanowane słowa'
            ]
        ],
        'create' => 'Dodaj wartość',
        'value' => 'Wartość',
    ],
    'model' => [
        'success' => [
            'store' => 'Pomyślnie wpisano na czarną listę.'
        ],
        'user' => [
            'user' => 'Użytkownik',
            'route' => [
                'index' => 'Zbanowani użytkownicy'
            ],
        ],
    ],
];
