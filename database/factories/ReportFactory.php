<?php

use Faker\Generator as Faker;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Report\Report;

$factory->define(Report::class, function (Faker $faker) {
    $content = $faker->text(100);

    return [
        'content' => $content,
    ];
});

$factory->state(Report::class, 'with_user', function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});
