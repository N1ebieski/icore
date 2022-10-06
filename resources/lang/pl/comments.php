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

use N1ebieski\ICore\ValueObjects\Comment\Status;

return [
    'post' => [
        'post' => 'Posty'
    ],
    'page' => [
        'page' => 'Podstrony'
    ],
    'success' => [
        'store' => [
            Status::INACTIVE => 'Komentarz został dodany i oczekuje na moderację.'
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
