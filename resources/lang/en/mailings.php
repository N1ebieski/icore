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
        Status::ACTIVE => 'active',
        Status::INACTIVE => 'inactive',
        Status::SCHEDULED => 'scheduled',
        Status::INPROGRESS => 'in progress'
    ],
    'route' => [
        'index' => 'Mailing',
        'edit' => 'Edit mailing',
        'create' => 'Add mailing'
    ]
];
