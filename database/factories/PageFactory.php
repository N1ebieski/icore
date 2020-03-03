<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Page\Page;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    $content = $faker->text(2000);

    return [
        'title' => $faker->sentence(3),
        'content_html' => $content,
        'content' => $content,
        'seo_title' => $faker->randomElement([$faker->sentence(5), null]),
        'seo_desc' => $faker->text(),
        'seo_noindex' => rand(0, 1),
        'seo_nofollow' => rand(0, 1),
        'status' => rand(0, 1),
        'comment' => rand(0, 1)
    ];
});

$factory->state(Page::class, 'active', function () {
    return [
        'status' => 1
    ];
});

$factory->state(Page::class, 'commentable', function (Faker $faker) {
    return [
        'comment' => 1
    ];
});

$factory->state(Page::class, 'not_commentable', function (Faker $faker) {
    return [
        'comment' => 0
    ];
});

$factory->state(Page::class, 'with_user', function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});
