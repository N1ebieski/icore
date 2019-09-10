<?php

namespace Seeds\ICore;

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

        // $posts->each(function($post) use ($users) {
            for ($i=0; $i<100; $i++) {
                $com[$i] = factory(Comment::class)->make();
                $com[$i]->morph()->associate($post);
                $com[$i]->user()->associate($users->random()->id);
                $com[$i]->save();

                for ($j=0; $j<rand(2, 10); $j++) {
                    $comcom[$j] = factory(Comment::class)->make();
                    $comcom[$j]->morph()->associate($post);
                    $comcom[$j]->user()->associate($users->random()->id);
                    $comcom[$j]->parent_id = $com[$i]->id;
                    $comcom[$j]->save();

                    for ($k=0; $k<rand(0, 5); $k++) {
                        $comcomcom[$k] = factory(Comment::class)->make();
                        $comcomcom[$k]->morph()->associate($post);
                        $comcomcom[$k]->user()->associate($users->random()->id);
                        $comcomcom[$k]->parent_id = $comcom[$j]->id;
                        $comcomcom[$k]->save();

                        if (rand(0, 1) == 1) {
                            for ($l=0; $l<rand(0, 5); $l++) {
                                $comcomcomcom[$l] = factory(Comment::class)->make();
                                $comcomcomcom[$l]->morph()->associate($post);
                                $comcomcomcom[$l]->user()->associate($users->random()->id);
                                $comcomcomcom[$l]->parent_id = $comcomcom[$k]->id;
                                $comcomcomcom[$l]->save();
                            }
                        }
                    }
                }
            }
        // });
    }
}
