<?php

use N1ebieski\ICore\Models\Mailing;

return [
    'create' => 'Add mailing',
    'reset' => 'Reset',
    'confirm' => 'Are you sure you want to reset the mailing? This will remove all existing ecipients and allow you to define them again on the edit page.',
    'title' => 'Title',
    'content' => 'Content',
    'emails_json' => 'List of e-mail addresses in JSON format',
    'success' => [
        'store' => 'Mailing for :recipients recipients has been added.',
        'update' => 'Mailing has changed.',
        'destroy_global' => 'Successfully deleted :affected mailings.'
    ],
    'activation_at' => [
        'label' => 'Activation date',
        'tooltip' => 'If the status is scheduled, activation will be deferred until then'
    ],
    'recipients' => 'Recipients',
    'users' => 'users',
    'subscribers' => 'newsletter subscribers',
    'custom' => 'own database of e-mail addresses',
    'status' => [
        Mailing::ACTIVE => 'active',
        Mailing::INACTIVE => 'inactive',
        Mailing::SCHEDULED => 'scheduled',
        Mailing::INPROGRESS => 'in progress'
    ],
    'route' => [
        'index' => 'Mailing',
        'edit' => 'Edit mailing',
        'create' => 'Add mailing'
    ]
];
