<?php

namespace N1ebieski\ICore\Database\Seeders\Env;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Category\Post\Category;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::get(['id'])->pluck('id')->toArray();

        User::makeFactory()->count(100)->user()->create()
            ->each(function ($u) use ($categories) {
                for ($i = 0; $i < rand(0, 5); $i++) {
                    $post = $u->posts()
                        ->save(
                            rand(0, 1) === 1 ?
                                Post::makeFactory()->image()->make()
                                : Post::makeFactory()->make()
                        );
                    $post->tag(Faker::create()->words(rand(1, 5)));
                    shuffle($categories);
                    $post->categories()->attach(array_slice($categories, 0, rand(1, 5)));
                }
            });
    }
}
