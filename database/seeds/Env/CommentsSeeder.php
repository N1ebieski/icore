<?php

namespace N1ebieski\ICore\Seeds\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Post\Comment;

/**
 * [CommentsSeeder description]
 */
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
        $post = Post::first();
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
                $comment = factory(Comment::class)->make();
                $comment->parent_id = $parent_id;
                $comment->morph()->associate($post);
                $comment->user()->associate($users->random()->id);
                $comment->save();

                $depth = $comment->real_depth + 1;

                if (isset($pattern[$depth])) {
                    $closure($comment->id);
                }
            }
        };

        $closure(null);
    }
}
