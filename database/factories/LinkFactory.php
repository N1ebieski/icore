<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use N1ebieski\ICore\Models\Link;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
        'name' => $faker->sentence(2)
    ];
});

$factory->state(Link::class, 'link', function (Faker $faker) {
    return [
        'type' => 'link',
    ];
});

$factory->state(Link::class, 'backlink', function (Faker $faker) {
    return [
        'type' => 'backlink',
    ];
});
