<?php

use Faker\Generator as Faker;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Post\Comment;

$factory->define(Comment::class, function (Faker $faker) {

    $content = $faker->text(300);

    return [
        'content_html' => $content,
        'content' => $content,
        'status' => rand(0, 1),
    ];
});

$factory->state(Comment::class, 'active', function (Faker $faker) {
    return [
        'status' => 1
    ];
});

$factory->state(Comment::class, 'with_user', function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});
