<?php

use N1ebieski\ICore\Models\Comment\Comment;

return [
    'post' => [
        'post' => 'Posty'
    ],
    'page' => [
        'page' => 'Podstrony'
    ],
    'success' => [
        'store' => [
            Comment::INACTIVE => 'Komentarz został dodany i oczekuje na moderację.'
        ],
        'destroy_global' => 'Pomyślnie usunięto :affected komentarzy wraz z odpowiedziami.',
    ],
    'route' => [
        'index' => 'Komentarze',
        'edit' => 'Edycja komentarza',
        'create' => 'Dodaj komentarz',
        'show_disqus' => 'Fragment dyskusji'
    ],
    'content' => 'Treść komentarza',
    'parent_id' => 'Komentarz nadrzędny',
    'null' => 'Brak',
    'confirm' => 'Czy na pewno chcesz usunąć komentarz wraz ze wszystkimi podrzędnymi?',
    'create' => 'Dodaj komentarz',
    'created_at_diff' => 'Dodano',
    'author' => 'Autor',
    'disqus' => 'Dyskusja w temacie :name',
    'reports' => 'Raportów',
    'published_at' => 'Opublikowano',
    'censored' => 'Komentarz usunięty.',
    'answer' => 'Odpowiedz',
    'edit' => 'Edycja',
    'report' => 'Raport',
    'log_to_answer' => 'Zaloguj się, by odpowiedzieć',
    'log_to_comment' => 'Zaloguj się, by skomentować',
    'next_answers' => 'Wczytaj odpowiedzi',
    'next_comments' => 'Wczytaj następne komentarze',
    'comments' => 'Komentarze',
    'latest' => 'Ostatnio skomentowane'
];
