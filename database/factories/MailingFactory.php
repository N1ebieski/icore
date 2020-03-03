<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\Mailing;
use Faker\Generator as Faker;

$factory->define(Mailing::class, function (Faker $faker) {
    $content = $faker->text(2000);

    return [
        'title' => $faker->sentence(5),
        'content_html' => $content,
        'content' => $content,
        'status' => 0
    ];
});

$factory->state(Mailing::class, 'active', function (Faker $faker) {
    return [
        'status' => 1
    ];
});
