<?php

use N1ebieski\ICore\Models\Post;

return [
    'success' => [
        'store' => 'Post został dodany.',
        'update' => 'Post został zmieniony.',
        'destroy_global' => 'Pomyślnie usunięto :affected postów.'
    ],
    'route' => [
        'blog' => 'Blog',
        'index' => 'Posty',
        'edit' => 'Edycja postu',
        'create' => 'Dodaj post',
        'search' => 'Wyszukiwanie: :search'
    ],
    'title' => 'Tytuł',
    'content' => 'Treść',
    'published_at_diff' => 'Opublikowano',
    'author' => 'Autor',
    'published_at' => [
        'label' => 'Data publikacji',
        'tooltip' => 'Jeśli data jest przyszła, publikacja zostanie odroczona do tego momentu'
    ],
    'tags' => [
        'label' => 'Tagi',
        'tooltip' => 'Min 3 znaki, max 30 znaków, max :max_tags tagów',
        'placeholder' => 'Dodaj tagi'
    ],
    'seo' => [
        'tooltip' => 'Używany w META oraz Open Graph',
    ],
    'draft' => 'szkic',
    'create' => 'Dodaj post',
    'more' => 'pokaż więcej &raquo',
    'comment' => 'Komentarze włączone',
    'status' => [
        Post::ACTIVE => 'aktywne',
        Post::INACTIVE => 'nieaktywne',
        Post::SCHEDULED => 'zaplanowane',
    ],
    'related' => 'Podobne posty'
];
