<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\BanValue;
use Faker\Generator as Faker;

$factory->define(BanValue::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(BanValue::class, 'ip', function (Faker $faker) {
    return [
        'type' => 'ip',
        'value' => $faker->ipv4
    ];
});
