<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\Rating\Rating;
use Faker\Generator as Faker;

$factory->define(Rating::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Rating::class, 'one', function () {
    return [
        'rating' => 1
    ];
});
