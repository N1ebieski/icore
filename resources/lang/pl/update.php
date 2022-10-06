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
    'update' => 'Uwaga: aktualizator zaktualizuje aktualne pliki aplikacji.',
    'rollback' => 'Uwaga: aktualizator przywróci poprzednie pliki aplikacji.',
    'confirm' => 'Czy chcesz kontynuować?',
    'validate' => [
        'backup' => 'Sprawdzanie istniejącej kopii bezpieczeństwa...',
    ],
    'errors' => [
        'backup' => [
            'exists' => 'Istnieje już kopia bezpieczeństwa dla tej wersji. Nie możesz ponownie aktualizować już raz zaktualizowanych plików. Przywróć wpierw poprzedni stan plików i usuń kopię bezpieczeństwa.',
            'no_exists' => 'Nie istnieje kopia bezpieczeństwa dla tej wersji.'
        ]
    ],
    'backup' => 'Tworzenie kopii bezpieczeństwa...',
    'update_files' => 'Aktualizowanie plików...',
    'rollback_files' => 'Przywracanie plików...'
];
