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

return [
    'post' => [
        'post' => 'Posty'
    ],
    'success' => [
        'store' => 'Kategoria została dodana',
        'store_global' => 'Drzewo kategorii zostało dodane',
        'store_parent' => ' do kategorii nadrzędnej :parent',
        'destroy_global' => 'Pomyślnie usunięto :affected kategorii wraz z kategoriami podrzędnymi.'
    ],
    'error' => [
        'search' => 'Nie znaleziono żadnej kategorii.'
    ],
    'route' => [
        'index' => 'Kategorie',
        'edit' => 'Edycja kategorii',
        'create' => 'Dodaj kategorie',
        'edit_position' => 'Edycja pozycji',
        'show' => 'Kategoria: :category'
    ],
    'name' => 'Nazwa',
    'names_json' => 'Nazwy kategorii w formacie JSON',
    'clear' => 'Usuń wszystkie dotychczasowe kategorie przed importem?',
    'parent_id' => 'Kategoria nadrzędna',
    'null' => 'Brak',
    'ancestors' => 'Kategoria i podrzędne należą do kategorii',
    'confirm' => 'Czy na pewno chcesz usunąć kategorie wraz ze wszystkimi podrzędnymi?',
    'create' => 'Dodaj kategorie',
    'categories' => [
        'label' => 'Kategorie',
        'tooltip' => 'Min 1 kategoria, max :max_categories kategorii'
    ],
    'search_categories' => 'Szukaj kategorii [min 3 znaki]',
    'position' => 'Pozycja',
    'roots' => 'główne',
    'icon' => [
        'label' => 'Klasa ikony',
        'tooltip' => 'Klasa ikony (np. z font-awesome). Ikona będzie wyświetlana obok nazwy.',
        'placeholder' => 'przykład font-awesome: fab fa-google'
    ]
];
