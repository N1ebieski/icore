<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Database\Seeders\Env;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\PostLang\PostLang;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Database\Factories\PostLang\PostLangFactory;

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

        User::makeFactory()->count(1)->user()->create()
            ->each(function ($user) use ($categories) {
                for ($i = 0; $i < rand(0, 5); $i++) {
                    /** @var Post */
                    $post = Post::makeFactory()->make();

                    $post->user()->associate($user);

                    $post->save();

                    $post->tag(Faker::create()->words(rand(1, 5)));

                    shuffle($categories);

                    $post->categories()->attach(array_slice($categories, 0, rand(1, 5)));

                    /** @var PostLang */
                    $postLang = PostLang::makeFactory()
                        ->when(rand(0, 1) === 1, function (PostLangFactory $factory) {
                            return $factory->image();
                        })
                        ->make();

                    $postLang->post()->associate($post);

                    $postLang->save();
                }
            });
    }
}
