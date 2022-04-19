<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Crons\PostCron;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Http\Response as HttpResponse;
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

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostShow()
    {
        $category = Category::makeFactory()->active()->create();

        $post = Post::makeFactory()->active()->publish()->withUser()->hasAttached($category)->create();

        $tags = Tag::makeFactory()->hasAttached($post, [], 'morphs')->count(5)->create();

        $response = $this->get(route('web.post.show', [$post->slug]));

        $response->assertSee($post->title, false);
        $response->assertSee($category->nam, false);
        $response->assertSee($post->user->name, false);
        $response->assertSee($tags[0]->name, false);
    }

    public function testPostShowCommentsDisable()
    {
        $post = Post::makeFactory()->active()->publish()->withUser()->notCommentable()->create();

        $comment = Comment::makeFactory()->count(50)->active()->withUser()->for($post, 'morph')->create();

        $response = $this->get(route('web.post.show', [$post->slug]));

        $response->assertDontSee($comment[1]->content);
        $response->assertSee($post->title, false);
    }

    public function testPostShowPaginate()
    {
        $post = Post::makeFactory()->active()->publish()->withUser()->commentable()->create();

        $comment = Comment::makeFactory()->count(50)->active()->withUser()->for($post, 'morph')->create();

        $response = $this->get(route('web.post.show', [
            $post->slug,
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertSee('class="pagination"', false);
        $response->assertSee($post->title, false);
        $response->assertSee($comment[30]->content, false);
    }

    public function testPostPublishScheduled()
    {
        $post = Post::makeFactory()->scheduled()->withUser()->commentable()->create();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Post::SCHEDULED
        ]);

        // Uruchamiamy zadanie crona bezpośrednio, bo przez schedule:run ma ustalony delay
        // (np. odpala się co godzinę)
        $schedule = app()->make(PostCron::class);
        $schedule();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Post::ACTIVE
        ]);
    }
}
