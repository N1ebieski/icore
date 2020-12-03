<?php

use N1ebieski\ICore\Models\Mailing;

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
        Mailing::ACTIVE => 'aktywne',
        Mailing::INACTIVE => 'nieaktywne',
        Mailing::SCHEDULED => 'zaplanowane',
        Mailing::INPROGRESS => 'w realizacji'
    ],
    'route' => [
        'index' => 'Mailing',
        'edit' => 'Edycja mailingu',
        'create' => 'Dodaj mailing'
    ]
];
