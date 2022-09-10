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

namespace N1ebieski\ICore\Tests\Feature\Web;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Crons\PostCron;
use N1ebieski\ICore\Models\Tag\Post\Tag;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Post\Status;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function testPostIndex(): void
    {
        $response = $this->get(route('web.post.index'));

        $response->assertViewIs('icore::web.post.index');
    }

    public function testNoexistPostShow(): void
    {
        $response = $this->get(route('web.post.show', ['dajskruiufi']));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testPostShow(): void
    {
        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->hasAttached($category)->create();

        /** @var Collection<Tag> */
        $tags = Tag::makeFactory()->hasAttached($post, [], 'morphs')->count(5)->create();

        $response = $this->get(route('web.post.show', [$post->slug]));

        $response->assertSee($post->title, false)
            ->assertSee($category->name, false)
            ->assertSee($post->user->name, false)
            // @phpstan-ignore-next-line
            ->assertSee($tags[0]->name, false);
    }

    public function testPostShowCommentsDisable(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->notCommentable()->create();

        /** @var Collection<Comment> */
        $comments = Comment::makeFactory()->count(50)->active()->withUser()->for($post, 'morph')->create();

        $response = $this->get(route('web.post.show', [$post->slug]));

        // @phpstan-ignore-next-line
        $response->assertDontSee($comments[1]->content)
            ->assertSee($post->title, false);
    }

    public function testPostShowPaginate(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->active()->publish()->withUser()->commentable()->create();

        /** @var Collection<Comment>|array<Comment> */
        $comments = Comment::makeFactory()->count(50)->active()->withUser()->for($post, 'morph')->create();

        $response = $this->get(route('web.post.show', [
            $post->slug,
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertSee('class="pagination"', false)
            ->assertSee($post->title, false)
            ->assertSee($comments[30]->content, false);
    }

    public function testPostPublishScheduled(): void
    {
        /** @var Post */
        $post = Post::makeFactory()->scheduled()->withUser()->commentable()->create();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Status::SCHEDULED
        ]);

        // Uruchamiamy zadanie crona bezpośrednio, bo przez schedule:run ma ustalony delay
        // (np. odpala się co godzinę)
        $schedule = app()->make(PostCron::class);
        $schedule();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => Status::ACTIVE
        ]);
    }
}
