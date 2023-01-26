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
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteCommentPostTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->delete(route('admin.comment.destroy', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);

        $response = $this->delete(route('admin.comment.destroy', [$comment->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function testDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Collection<Comment> */
        $comments = Comment::makeFactory()->count(10)->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $this->get(route('admin.comment.post.index'));

        $select = collect($comments)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.comment.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.comment.post.index'));
        $response->assertSessionHas('success');

        $deleted = Comment::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);
    }
}
