<?php

return [
    'success' => [
        'store' => 'Post został dodany.',
        'update' => 'Post został zmieniony.',
        'destroy_global' => 'Pomyślnie usunięto :affected postów.'
    ],
    'page' => [
        'blog' => 'Blog',
        'index' => 'Posty',
        'edit' => 'Edycja postu',
        'create' => 'Dodaj post',
        'search' => 'Wyszukiwanie: :search'
    ],
    'title' => 'Tytuł',
    'content' => 'Treść',
    'published_at' => 'Data publikacji',
    'published_at_diff' => 'Opublikowano',
    'author' => 'Autor',
    'published_at_tooltip' => 'Jeśli data jest przyszła, publikacja zostanie odroczona do tego momentu',
    'tags' => 'Tagi',
    'tags_tooltip' => 'Min 3 znaki, max 30 znaków, max :max_tags tagów',
    'seo_tooltip' => 'Używany w META oraz Open Graph',
    'draft' => 'szkic',
    'create' => 'Dodaj post',
    'more' => 'pokaż więcej &raquo',
    'comment' => 'Komentarze włączone',
    // Przeniesione statusy z filter, bo w przypadku postów jest dodatkowy stan
    'status' => [
        '1' => 'aktywne',
        '0' => 'nieaktywne',
        '2' => 'zaplanowane',
    ],
    'related' => 'Podobne posty'
];
