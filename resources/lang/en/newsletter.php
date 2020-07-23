<?php

use N1ebieski\ICore\Models\Newsletter;

return [
    'subscribe' => 'Subscribe to our newsletter',
    'email' => [
        'placeholder' => 'Enter your e-mail address'
    ],
    'success' => [
        'store' => 'Thanks for subscribing. To your email address, we sent a message with a confirmation link to subscription.',
        'update_status' => [
            Newsletter::ACTIVE => 'Subscription was successfully activated.',
            Newsletter::INACTIVE => 'Subscription was successfully deactivated.'
        ],
    ],
    'subscribe_confirmation' => 'Newsletter subscription confirmation',
    'subscribe_confirm' => 'Confirm your subscription',
    'subcopy' => [
        'subscribe' => 'You get this message because you subscribe our newsletter. If you no longer want to receive messages, click on the link <a href=":cancel">:cancel</a>'
    ],
    'mail' => [
        'subscribe_confirm' => [
            'info' => 'Please click on the button below to confirm your subscription our newsletter at this email address.',
            'token' => 'The link to confirm your subscription is valid for 60 minutes.',
        ]
    ]
];
