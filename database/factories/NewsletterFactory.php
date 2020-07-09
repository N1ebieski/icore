<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\Newsletter;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Newsletter::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'status' => rand(0, 1)
    ];
});

$factory->state(Newsletter::class, 'active', function () {
    return [
        'status' => 1
    ];
});

$factory->state(Newsletter::class, 'inactive', function () {
    return [
        'status' => 0
    ];
});
