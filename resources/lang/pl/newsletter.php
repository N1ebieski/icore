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

use N1ebieski\ICore\ValueObjects\Newsletter\Status;

return [
    'subscribe' => 'Subskrybuj newsletter',
    'email' => [
        'placeholder' => 'Wpisz adres email'
    ],
    'success' => [
        'store' => 'Dziękujemy za subskrypcję. Na podany adres email wysłano wiadomość z linkiem potwierdzającym chęć otrzymywania newslettera.',
        'update_status' => [
            Status::ACTIVE => 'Pomyślnie aktywowano subskrypcję.',
            Status::INACTIVE => 'Pomyślnie dezaktywowano subskrypcję.'
        ],
    ],
    'subscribe_confirmation' => 'Potwierdzenie subskrypcji newslettera',
    'subscribe_confirm' => 'Potwierdź subskrypcję',
    'subcopy' => [
        'subscribe' => 'Otrzymujesz tą wiadomość ponieważ subskrybujesz newsletter na naszej stronie. Jeżeli nie chcesz dłużej otrzymywać wiadomości, kliknij w link <a href=":cancel">:cancel</a>',
        'user' => 'Otrzymujesz tą wiadomość ponieważ masz zarejestrowane konto na naszej stronie i wyraziłeś zgodę na "informacje marketingowe". Jeżeli nie chcesz dłużej otrzymywać wiadomości, kliknij w link <a href=":cancel">:cancel</a> i po zalogowaniu na swoje konto odznacz "informacje marketingowe".'
    ],
    'mail' => [
        'subscribe_confirm' => [
            'info' => 'Proszę kliknąć na przycisk poniżej aby potwierdzić subskrypcję newslettera pod tym adresem email.',
            'token' => 'Link do potwierdzenia subskrybcji ważny jest przez 60 minut.',
        ]
    ]
];
