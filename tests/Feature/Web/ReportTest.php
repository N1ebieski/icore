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
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    public function testReportCommentCreateAsGuest(): void
    {
        $response = $this->get(route('web.report.comment.create', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testReportNoexistCommentCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testReportInactiveCommentCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->inactive()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testReportCommentCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->get(route('web.report.comment.create', [$comment->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(route('web.report.comment.store', [$comment->id]), $baseResponse->getData()->view);
    }

    public function testReportCommentStoreAsGuest(): void
    {
        $response = $this->post(route('web.report.comment.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testReportNoexistCommentStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testReportInactiveCommentStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->inactive()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testReportCommentStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function testReportCommentStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Comment */
        $comment = Comment::makeFactory()->active()->withUser()->withMorph()->create();

        Auth::login($user);

        $response = $this->post(route('web.report.comment.store', [$comment->id]), [
            'content' => 'Ten <b>komentarz</b> jest zły. <script>Usunąć!</script>'
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::reports.success.store')
        ]);

        $this->assertDatabaseHas('reports', [
            'model_id' => $comment->id,
            'model_type' => $comment->getMorphClass(),
            'content' => 'Ten komentarz jest zły. Usunąć!'
        ]);
    }
}
