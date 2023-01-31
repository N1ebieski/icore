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

namespace N1ebieski\ICore\Tests\Feature\Admin\Comment\Post;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.comment.post.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testCreateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.create', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.create', [$post->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreateChildren(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.post.create', [$post->id, 'parent_id' => $parent->id]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.comment.post.store', [$post->id]),
            $baseResponse->getData()->view
        );
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.comment.post.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testStoreNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreChildrenNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => 3894839
        ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    public function testStoreChildren(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $parent = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->post(route('admin.comment.post.store', [$post->id]), [
            'content' => '<b>Komentarz</b> zostal dodany. <script>dsdasd</script>',
            'parent_id' => $parent->id
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Komentarz zostal dodany. dsdasd',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('comments', [
            'content' => 'Komentarz zostal dodany. dsdasd',
            'parent_id' => $parent->id,
            'model_id' => $post->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\Post'
        ]);
    }
}
