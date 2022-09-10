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

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    public function testReportCommentShowAsGuest(): void
    {
        $response = $this->get(route('admin.report.comment.show', [32]));

        $response->assertRedirect(route('login'));
    }

    public function testReportCommentShowWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.report.comment.show', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testReportNoexistCommentShow(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.report.comment.show', [4343434]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testReportCommentShow(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Post */
        $post = Post::makeFactory()->active()->commentable()->publish()->withUser()->create();

        /** @var User */
        $comment = Comment::makeFactory()->active()->withUser()->for($post, 'morph')->create();

        /** @var array<Report> */
        $reports = Report::makeFactory()->count(10)->withUser()->for($comment, 'morph')->create();

        Auth::login($user);

        $response = $this->get(route('admin.report.comment.show', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString($reports[6]->content, $baseResponse->getData()->view);
    }

    public function testReportCommentClearAsGuest(): void
    {
        $response = $this->delete(route('admin.report.comment.clear', [32]));

        $response->assertRedirect(route('login'));
    }

    public function testReportCommentClearWithoutPermission(): void
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

    public function testReportNoexistCommentClear(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.report.comment.clear', [4343434]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testReportCommentClear(): void
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
