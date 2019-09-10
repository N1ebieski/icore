<?php

use Faker\Generator as Faker;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use Carbon\Carbon;

$factory->define(Post::class, function(Faker $faker) {

    $content = $faker->text(2000);

    return [
        'title' => $faker->sentence(5),
        'content_html' => $content,
        'content' => $content,
        'seo_title' => $faker->randomElement([$faker->sentence(5), null]),
        'seo_desc' => $faker->text(),
        'seo_noindex' => rand(0, 1),
        'seo_nofollow' => rand(0, 1),
        'status' => rand(0, 1),
        'comment' => rand(0, 1),
        'published_at' => $faker->randomElement([
            $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            null
        ]),
    ];
});

$factory->state(Post::class, 'active', function(Faker $faker) {
    return [
        'status' => 1
    ];
});

$factory->state(Post::class, 'commentable', function(Faker $faker) {
    return [
        'comment' => 1
    ];
});

$factory->state(Post::class, 'not_commentable', function(Faker $faker) {
    return [
        'comment' => 0
    ];
});

$factory->state(Post::class, 'publish', function(Faker $faker) {
    return [
        'published_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s')
    ];
});

$factory->state(Post::class, 'scheduled', function(Faker $faker) {
    return [
        'status' => 2,
        'published_at' => Carbon::now()->format('Y-m-d H:i:s')
    ];
});

$factory->state(Post::class, 'with_user', function(Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});
