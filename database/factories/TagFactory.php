<?php

use Faker\Generator as Faker;
use N1ebieski\ICore\Models\Tag\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => ucfirst($faker->word)
    ];
});
