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
    'route' => [
        'index' => 'Tagi',
        'show' => 'Tag: :tag',
        'create' => 'Dodaj tag',
        'edit' => 'Edytuj tag'
    ],
    'success' => [
        'store' => 'Tag został dodany',
        'destroy_global' => 'Pomyślnie usunięto :affected tagów.'
    ],
    'popular' => 'Popularne tagi',
    'name' => 'Nazwa',
    'sum' => 'suma'
];
