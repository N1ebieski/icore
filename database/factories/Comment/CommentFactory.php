<?php

use Faker\Generator as Faker;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Comment;

$factory->define(Comment::class, function (Faker $faker) {

    $content = $faker->text(300);

    return [
        'content_html' => $content,
        'content' => $content,
        'status' => rand(0, 1),
    ];
});

$factory->state(Comment::class, 'active', function () {
    return [
        'status' => 1
    ];
});

$factory->state(Comment::class, 'inactive', function () {
    return [
        'status' => 0
    ];
});

$factory->state(Comment::class, 'with_user', function () {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});

$factory->afterMakingState(Comment::class, 'with_post', function ($comment) {
    $comment->morph()->associate(
        factory(Post::class)->states(['active', 'commentable', 'publish', 'with_user'])->create()
    );
});
