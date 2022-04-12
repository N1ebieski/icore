<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use Faker\Factory as Faker;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Crons\PostCron;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function testPostIndex()
    {
        $response = $this->get(route('web.post.index'));

        $response->assertViewIs('icore::web.post.index');
    }

    public function testNoexistPostShow()
    {
        $response = $this->get(route('web.post.show', ['dajskruiufi']));

        $response->assertStatus(404);
    }

    public function testPostShow()
    {
        $category = factory(Category::class)->states('active')->create();

        $post = factory(Post::class)->states(['active', 'publish', 'with_user'])->create();
        $post->categories()->attach($category);

        $tag = Faker::create()->word;
        $post->tag($tag);

        $response = $this->get(route('web.post.show', [$post->slug]));

        $response->assertSee($post->title, false);
        $response->assertSee($category->nam, false);
        $response->assertSee($post->user->name, false);
        $response->assertSee($tag, false);
    }

    public function testPostShowCommentsDisable()
    {
        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'not_commentable'])
            ->create();

        $comment = factory(Comment::class, 50)
            ->states(['active', 'with_user'])
            ->make()
            ->each(function ($item) use ($post) {
                $item->morph()->associate($post)->save();
            });

        $response = $this->get(route('web.post.show', [$post->slug]));

        $response->assertDontSee($comment[1]->content);
        $response->assertSee($post->title, false);
    }

    public function testPostShowPaginate()
    {
        $post = factory(Post::class)->states(['active', 'publish', 'with_user', 'commentable'])
            ->create();

        $comment = factory(Comment::class, 50)
            ->states(['active', 'with_user'])
            ->make()
            ->each(function ($item) use ($post) {
                $item->morph()->associate($post)->save();
            });

        $response = $this->get(route('web.post.show', [
            $post->slug,
            'page' => 2,
            'orderby' => 'created_at|asc'
        ]));

        $response->assertSee('class="pagination"', false);
        $response->assertSee($post->title, false);
        $response->assertSee($comment[30]->content, false);
    }

    public function testPostPublishScheduled()
    {
        $post = factory(Post::class)->states(['scheduled', 'with_user', 'commentable'])
            ->create();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 2
        ]);

        // Uruchamiamy zadanie crona bezpośrednio, bo przez schedule:run ma ustalony delay
        // (np. odpala się co godzinę)
        $schedule = app()->make(PostCron::class);
        $schedule();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 1
        ]);
    }
}
