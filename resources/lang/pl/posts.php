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

use N1ebieski\ICore\ValueObjects\Post\Status;

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
        'tooltip' => 'Min 3 znaki, max :max_chars znaków, max :max_tags tagów',
        'placeholder' => 'Dodaj tagi'
    ],
    'seo' => [
        'tooltip' => 'Używany w META oraz Open Graph',
    ],
    'draft' => 'szkic',
    'create' => 'Dodaj post',
    'more' => 'pokaż więcej &raquo;',
    'comment' => 'Komentarze włączone',
    'status' => [
        Status::ACTIVE => 'aktywne',
        Status::INACTIVE => 'nieaktywne',
        Status::SCHEDULED => 'zaplanowane',
    ],
    'related' => 'Podobne posty',
    'chart' => [
        'x' => [
            'label' => 'Data'
        ],
        'y' => [
            'label' => 'Ilość treści'
        ],
        'count_by_date' => 'Wykres ilości postów i podstron na osi czasu'
    ]
];
