<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\MailingEmail;
use Faker\Generator as Faker;

$factory->define(MailingEmail::class, function(Faker $faker) {
    return [
        //
    ];
});

$factory->state(MailingEmail::class, 'with_user', function() {
    return [
        'model_id' => function() {
            return factory(User::class)->create()->id;
        },
        'model_type' => function(array $mailingEmail) {
            return User::class;
        }
    ];
});

$factory->state(MailingEmail::class, 'with_email', function(Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail
    ];
});
