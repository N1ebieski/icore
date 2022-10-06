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

use N1ebieski\ICore\ValueObjects\BanValue\Type;

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
        Type::IP => [
            'ip' => 'Adres IP',
            'route' => [
                'index' => 'Zbanowane adresy IP'
            ],
        ],
        Type::WORD => [
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
