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

use N1ebieski\ICore\ValueObjects\Page\Status;

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
    'author' => 'Autor',
    'status' => [
        Status::ACTIVE => 'aktywne',
        Status::INACTIVE => 'nieaktywne'
    ],
    'position' => 'Pozycja',
    'pages' => 'Podstrony',
    'map' => 'Zobacz również',
    'more' => 'pokaż więcej &raquo;',
];
