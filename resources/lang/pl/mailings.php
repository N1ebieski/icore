<?php

return [
    'create' => 'Dodaj mailing',
    'reset' => 'Reset',
    'confirm' => 'Czy na pewno chcesz zresetować mailing? Spowoduje to usunięcie
    wszystkich dotychczasowych odbiorców i pozwoli zdefiniować ich ponownie w oknie edycji.',
    'title' => 'Temat wiadomości',
    'content' => 'Treść wiadomości',
    'emails_json' => 'Lista adresów email w formacie JSON',
    'success' => [
        'store' => 'Mailing dla :recipients odbiorców został dodany.',
        'update' => 'Mailing został zmieniony.',
        'destroy_global' => 'Pomyślnie usunięto :affected mailingów.'
    ],
    'activation_at' => 'Data aktywacji',
    'activation_at_tooltip' => 'Jeśli status jest zaplanowany, aktywacja zostanie odroczona do tego momentu',
    'recipients' => 'Odbiorcy',
    'users' => 'użytkownicy',
    'subscribers' => 'subskrybenci newslettera',
    'custom' => 'własna baza adresów email',
    // Przeniesione statusy z filter, bo w przypadku mailingów jest dodatkowy stan
    'status_1' => 'aktywne',
    'status_0' => 'nieaktywne',
    'status_2' => 'zaplanowane',
    'page' => [
        'index' => 'Mailing',
        'edit' => 'Edycja mailingu',
        'create' => 'Dodaj mailing'
    ]
];
