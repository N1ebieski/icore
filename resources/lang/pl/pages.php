<?php

use N1ebieski\ICore\Models\Page\Page;

return [
    'success' => [
        'store' => 'Podstrona została dodana',
        'store_parent' => ' do podstrony nadrzędnej :parent',
        'update' => 'Podstrona została zmieniona',
        'destroy_global' => 'Pomyślnie usunięto :affected podstron wraz z podstronami podrzędnymi'
    ],
    'route' => [
        'index' => 'Podstrony',
        'edit' => 'Edycja podstrony',
        'edit_position' => 'Edycja pozycji',
        'create' => 'Dodaj podstronę'
    ],
    'title' => 'Tytuł',
    'content' => 'Treść',
    'parent_id' => 'Podstrona nadrzędna',
    'null' => 'Brak',
    'ancestors' => 'Podstrona i podrzędne należą do podstrony',
    'confirm' => 'Czy na pewno chcesz usunąć podstronę wraz ze wszystkimi podrzędnymi?',
    'create' => 'Dodaj podstronę',
    'roots' => 'główne',
    'comment' => 'Komentarze włączone',
    'seo' => [
        'tooltip' => 'Używany w META oraz Open Graph'
    ],
    'tags' => [
        'label' => 'Tagi',
        'tooltip' => 'Min 3 znaki, max :max_chars znaków, max :max_tags tagów',
        'placeholder' => 'Dodaj tagi'
    ],
    'icon' => [
        'label' => 'Klasa ikony',
        'tooltip' => 'Klasa ikony (np. z font-awesome). Ikona będzie wyświetlana obok tytułu.',
        'placeholder' => 'przykład font-awesome: fab fa-google',
    ],
    'status' => [
        Page::ACTIVE => 'aktywne',
        Page::INACTIVE => 'nieaktywne'
    ],
    'position' => 'Pozycja',
    'pages' => 'Podstrony',
    'map' => 'Zobacz również',
    'more' => 'pokaż więcej &raquo',    
];
