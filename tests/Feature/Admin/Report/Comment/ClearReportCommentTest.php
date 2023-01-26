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

namespace N1ebieski\ICore\Tests\Feature\Admin\Report\Comment;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClearReportCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function testClearAsGuest(): void
    {
        $response = $this->delete(route('admin.report.comment.clear', [32]));

        $response->assertRedirect(route('login'));
    }

    public function testClearWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testClearNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [4343434]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testClear(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        /** @var Collection<Report> */
        $reports = Report::makeFactory()->count(10)->withUser()->for($comment, 'morph')->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        $deleted = Report::whereIn('id', collect($reports)->pluck('id')->toArray())->count();

        $this->assertTrue($deleted === 0);
    }
}
