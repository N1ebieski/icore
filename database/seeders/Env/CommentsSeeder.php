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

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Comment\Post\Comment;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$posts = Post::inRandomOrder()->limit(100)->get();

        /** @var Post */
        $post = Post::first();

        /** @var Collection<User> */
        $users = User::all();

        $pattern = [
            0 => 50,
            1 => [2, 10],
            2 => [0, 10],
            3 => [0, 5]
        ];

        $depth = 0;

        $closure = function ($parent_id) use ($pattern, $post, $users, &$closure, &$depth) {
            if (is_array($pattern[$depth])) {
                $loop = rand($pattern[$depth][0], $pattern[$depth][1]);
            } else {
                $loop = $pattern[$depth];
            }

            for ($i = 0; $i < $loop; $i++) {
                /** @var Comment */
                $comment = Comment::makeFactory()->for($post, 'morph')->for($users->random(), 'user')->create([
                    'parent_id' => $parent_id
                ]);

                $depth = $comment->real_depth + 1;

                if (isset($pattern[$depth])) {
                    $closure($comment->id);
                }
            }
        };

        $closure(null);
    }
}
