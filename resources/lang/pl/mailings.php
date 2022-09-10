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

use N1ebieski\ICore\ValueObjects\Mailing\Status;

return [
    'create' => 'Dodaj mailing',
    'reset' => 'Reset',
    'confirm' => 'Czy na pewno chcesz zresetować mailing? Spowoduje to usunięcie wszystkich dotychczasowych odbiorców i pozwoli zdefiniować ich ponownie w oknie edycji.',
    'title' => 'Temat wiadomości',
    'content' => 'Treść wiadomości',
    'emails_json' => 'Lista adresów email w formacie JSON',
    'success' => [
        'store' => 'Mailing dla :recipients odbiorców został dodany.',
        'update' => 'Mailing został zmieniony.',
        'destroy_global' => 'Pomyślnie usunięto :affected mailingów.'
    ],
    'activation_at' => [
        'label' => 'Data aktywacji',
        'tooltip' => 'Jeśli status jest zaplanowany, aktywacja zostanie odroczona do tego momentu'
    ],
    'recipients' => 'Odbiorcy',
    'users' => 'użytkownicy',
    'subscribers' => 'subskrybenci newslettera',
    'custom' => 'własna baza adresów email',
    'status' => [
        Status::ACTIVE => 'aktywne',
        Status::INACTIVE => 'nieaktywne',
        Status::SCHEDULED => 'zaplanowane',
        Status::INPROGRESS => 'w realizacji'
    ],
    'route' => [
        'index' => 'Mailing',
        'edit' => 'Edycja mailingu',
        'create' => 'Dodaj mailing'
    ]
];
