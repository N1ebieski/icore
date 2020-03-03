<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\MailingEmail;
use Faker\Generator as Faker;

$factory->define(MailingEmail::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(MailingEmail::class, 'with_user', function () {
    $user = factory(User::class)->create();

    return [
        'model_id' => $user->id,
        'model_type' => function () {
            return User::class;
        },
        'email' => $user->email
    ];
});

$factory->state(MailingEmail::class, 'with_email', function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail
    ];
});
