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

class UpdateCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.edit', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.comment.edit', [$comment->id]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.comment.update', [$comment->id]),
            $baseResponse->getData()->view
        );

        $this->assertStringContainsString(
            $comment->content,
            $baseResponse->getData()->view
        );
    }

    public function testUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [$comment->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [$comment->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function testUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->put(route('admin.comment.update', [$comment->id]), [
            'content' => '<b>Komentarz</b> został zaktualizowany. <script>dsadad</script>'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'Komentarz został zaktualizowany. dsadad',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Komentarz został zaktualizowany. dsadad'
        ]);
    }
}
